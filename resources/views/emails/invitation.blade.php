<p>Hi {{ $invitation->name }},</p>

<p>{{ $invitation->inviter->name }} has invited you to join <strong>{{ $invitation->company->name }}</strong> as {{ $invitation->role->label() }}.</p>

<p><a href="{{ $acceptUrl }}">Accept invitation</a></p>

<p>If the link does not work, copy this into your browser:<br>{{ $acceptUrl }}</p>
