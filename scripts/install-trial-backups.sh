#!/bin/sh
# Daily local recovery copies; keep the most recent seven. Copy off-server too.
set -eu
test "$(id -u)" -eq 0
test -f /opt/slodziak/.env.production
cat > /usr/local/sbin/slodziak-backup <<'SCRIPT'
#!/bin/sh
set -eu
umask 077
cd /opt/slodziak
sh scripts/backup.sh .env.production
# Only completed copies created by this script, oldest first.
python3 - <<'PY'
from pathlib import Path
import shutil
root = Path('/opt/slodziak/backups').resolve()
copies = sorted(p for p in root.iterdir() if p.is_dir() and not p.is_symlink()
                and (p / 'SHA256SUMS').is_file())
for copy in copies[:-7]:
    if copy.resolve().parent != root:
        raise RuntimeError('Unexpected backup directory')
    shutil.rmtree(copy)
PY
SCRIPT
chmod 0700 /usr/local/sbin/slodziak-backup
cat > /etc/systemd/system/slodziak-backup.service <<'UNIT'
[Unit]
Description=sLodziak database and media backup
After=docker.service
Requires=docker.service
[Service]
Type=oneshot
ExecStart=/usr/local/sbin/slodziak-backup
Nice=10
IOSchedulingClass=idle
UNIT
cat > /etc/systemd/system/slodziak-backup.timer <<'UNIT'
[Unit]
Description=Daily sLodziak recovery copy
[Timer]
OnCalendar=*-*-* 03:20:00 Europe/Warsaw
RandomizedDelaySec=300
Persistent=true
[Install]
WantedBy=timers.target
UNIT
systemctl daemon-reload
systemctl enable --now slodziak-backup.timer
