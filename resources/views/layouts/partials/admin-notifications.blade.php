                            <!-- Notifications Dropdown -->
                            @if(auth()->user()->role === 'admin')
                            <div class="relative items-center">
                                <x-dropdown align="right" width="64">
                                    <x-slot name="trigger">
                                        <button class="relative inline-flex items-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                            @if(isset($totalNotifications) && $totalNotifications > 0)
                                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">{{ $totalNotifications }}</span>
                                            @endif
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                                            Notifications
                                        </div>

                                        @if(isset($totalNotifications) && $totalNotifications > 0)
                                            @if($pendingUsers > 0)
                                                <x-dropdown-link :href="route('admin.taxpayers.index', ['status' => 'unverified'])">
                                                    <div class="flex justify-between items-center">
                                                        <span>Taxpayer Verify</span>
                                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 rounded">{{ $pendingUsers }}</span>
                                                    </div>
                                                </x-dropdown-link>
                                            @endif

                                            @if($pendingPayments > 0)
                                                <x-dropdown-link :href="route('admin.taxpayers.payments')">
                                                    <div class="flex justify-between items-center">
                                                        <span>Payments</span>
                                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 rounded">{{ $pendingPayments }}</span>
                                                    </div>
                                                </x-dropdown-link>
                                            @endif

                                            @if($pendingUploads > 0)
                                                <x-dropdown-link :href="route('admin.interviewers.index')">
                                                    <div class="flex justify-between items-center">
                                                        <span>Documents</span>
                                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 rounded">{{ $pendingUploads }}</span>
                                                    </div>
                                                </x-dropdown-link>
                                            @endif
                                        @else
                                            <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                                No new notifications
                                            </div>
                                        @endif
                                    </x-slot>
                                </x-dropdown>
                            </div>
                            @endif
