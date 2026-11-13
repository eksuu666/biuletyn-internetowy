$(".select2").select2();
$(".select2.round").select2({
    containerCssClass: "round"
});
$(".select2.curve").select2({
    containerCssClass: "curve"
});

$(".allow-cancel").select2({
    allowClear: true,
    placeholder: {
        id: "",
        placeholder: "..."
    }
});
