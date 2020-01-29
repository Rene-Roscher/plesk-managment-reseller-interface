<form id="orderForm" method="post">
    {{ csrf_field() }}
    <div class="col-xs-12 col-md-12">
        <div class="form-group">
            <label>Ausgewählte Konfiguration:</label><br>
            <p>
                Festplatte: {{ $disk->title }} (+{{ $disk->price }} €)<br>
                Domains: {{ $site->title }} ({{ $site->price }} €)<br>
                Subdomains: {{ $subdom->title }} (+{{ $subdom->price }} €)<br>
                Postfächer: {{ $mail->title }} (+{{ $mail->price }} €)<br>
                Datenbanken: {{ $db->title }} (+{{ $db->price }} €)<br>
                Ftp-Benutzer: {{ $ftp->title }} (+{{ $ftp->price }} €)<br>
                Traffic: Fair Use (+0.00 €)
            </p>

            <label>Bezahlung:</label><br>
            <p>
                Preis: {{ $price }}<br>
                Konto vorher: {{ $money_before }}<br>
                Preis: {{ $money_after }}<br>
                Vorauszahlung: {{ $runtime }} Tage<br>
            </p>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
            <button type="button" onclick="sendStep(2, this)" class="btn btn-primary">Kostenpflichtig Bestellen</button>
        </div>

        {{--<button type="button" onclick="sendStep(2, this)" class="btn btn-info" style="float: right">--}}
            {{--<span>Kostenpflichtig Bestellen</span>--}}
        {{--</button>--}}

        <input name="upgrade1" value="{{ $disk->id }}" hidden>
        <input name="upgrade2" value="{{ $site->id }}" hidden>
        <input name="upgrade3" value="{{ $subdom->id }}" hidden>
        <input name="upgrade4" value="{{ $mail->id }}" hidden>
        <input name="upgrade5" value="{{ $db->id }}" hidden>
        <input name="upgrade6" value="{{ $ftp->id }}" hidden>
        <input name="runtime" value="{{ $runtime }}" hidden>
        <input name="runtimePrice" value="{{ $runtimePrice }}" hidden>

    </div>
</form>
