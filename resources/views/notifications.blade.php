@extends('parts.base')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold mb-6">
        Notifications
    </h1>

    @forelse($notifications as $notification)

        <div class="bg-white shadow rounded-lg p-4 mb-4">

            <div class="flex justify-between">

                <div>

                    <h3 class="font-semibold">

                        @switch($notification->type)

                            @case('ticket_created')
                                🎫 Nouveau ticket
                            @break

                            @case('ticket_assigned')
                                👨‍💻 Ticket assigné
                            @break

                            @case('ticket_escalated')
                                🚨 Ticket escaladé
                            @break

                            @case('ticket_resolved')
                                ✅ Ticket résolu
                            @break

                            @default
                                🔔 Notification

                        @endswitch

                    </h3>

                    <p class="text-gray-600 mt-2">

                        Ticket :
                        #{{ $notification->ticket_id }}

                    </p>

                    <p class="text-sm text-gray-500">

                        {{ $notification->created_at->diffForHumans() }}

                    </p>

                </div>

                <div>

                    @if($notification->status == 'unread')

                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs">
                            Non lue
                        </span>

                    @else

                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs">
                            Lue
                        </span>

                    @endif

                </div>

            </div>

        </div>

    @empty

        <div class="bg-white p-6 rounded-lg shadow">

            Aucune notification disponible.

        </div>

    @endforelse

    {{ $notifications->links() }}

</div>

@endsection