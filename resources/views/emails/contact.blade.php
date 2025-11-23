<p>Er is een nieuw bericht via het contactformulier:</p>

<ul>
    <li><strong>Naam:</strong> {{ $data['name'] }}</li>
    <li><strong>E-mail:</strong> {{ $data['email'] }}</li>
</ul>

<p><strong>Bericht:</strong></p>
<p>{!! nl2br(e($data['message'])) !!}</p>
