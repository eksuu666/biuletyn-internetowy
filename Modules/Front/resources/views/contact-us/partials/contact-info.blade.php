<div class="widget contact-info">

    @if($contact->address)
        <div class="contact-info-box">
            <div class="contact-info-box-content">
                <h4>Adres redakcji</h4>
                <p>{{ $contact->address }}</p>
            </div>
        </div>
    @endif

    @if($contact->email)
        <div class="contact-info-box">
            <div class="contact-info-box-content">
                <h4>Napisz do nas</h4>
                <p>{{ $contact->email }}</p>
            </div>
        </div>
    @endif

    @if($contact->phone)
        <div class="contact-info-box">
            <div class="contact-info-box-content">
                <h4>Zadzwoń do nas</h4>
                <p><span class="ltr_text">{{ $contact->phone }}</span></p>
            </div>
        </div>
    @endif
</div><!-- Widget end -->
