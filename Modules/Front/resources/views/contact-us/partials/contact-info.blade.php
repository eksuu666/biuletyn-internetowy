<div class="widget contact-info">

    @if($contact->address)
        <div class="contact-info-box">
            <div class="contact-info-box-content">
                <h2>Adres redakcji</h2>
                <p>{{ $contact->address }}</p>
            </div>
        </div>
    @endif

    @if($contact->email)
        <div class="contact-info-box">
            <div class="contact-info-box-content">
                <h2>E-mail</h2>
                <p><a class="portal-service-link" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></p>
            </div>
        </div>
    @endif

    @if($contact->phone)
        <div class="contact-info-box">
            <div class="contact-info-box-content">
                <h2>Telefon</h2>
                <p><a class="portal-service-link" href="tel:{{ preg_replace('/[^0-9+]/', '', $contact->phone) }}">{{ $contact->phone }}</a></p>
            </div>
        </div>
    @endif
</div><!-- Widget end -->
