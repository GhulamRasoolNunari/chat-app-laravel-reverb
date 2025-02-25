<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        <!-- Sidebar (Groups & Users) -->
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                            <div class="card">
                                <div class="card-header py-5">Chats <hr class="mb-2"></div>
                                <div class="card-body">
                                    <!-- Group Chats -->
                                    <h6 class="">Groups:</h6>

                                    <ul class="list-group mb-3 ml-4">
                                        @foreach($groups as $group)
                                            <li class="list-group-item group-item" data-id="{{ $group->id }}">
                                                {{ $group->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                
                                    <!-- Direct Messages -->
                                    <h6>Users:</h6>
                                    <ul class="list-group mb-2 ml-4">
                                        @foreach($users as $user)
                                            @if($user->id !== auth()->id()) <!-- Exclude logged-in user -->
                                                <li class="list-group-item user-item" data-id="{{ $user->id }}">
                                                    {{ $user->name }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                
                        <!-- Chat Area -->
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <span id="chat-title">Select a chat</span>
                                </div>
                                <div class="card-body chat-box" id="chat-box" style="height: 400px; overflow-y: auto;">
                                    <!-- Messages will be loaded here dynamically -->
                                </div>
                                <div class="card-footer">
                                    <input type="hidden" id="chat-type" value="">
                                    <input type="hidden" id="chat-id" value="">
                                    <div class="input-group">
                                        <input type="text" id="message-input" class="form-control" placeholder="Type a message">
                                        <button class="btn btn-primary" id="send-btn">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
