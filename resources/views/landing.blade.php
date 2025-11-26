<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dire Dawa City Tax Information Management System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
    <div class="relative flex-1 overflow-hidden">
        <!-- Background decorations -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -left-32 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 h-[28rem] w-[28rem] rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-sky-500/40 via-slate-900/0 to-transparent"></div>
        </div>

        <header class="relative z-10 border-b border-white/5 bg-slate-950/70 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-lg shadow-indigo-600/40">
                        <span class="text-lg font-semibold tracking-tight">DD</span>
                    </div>
                    <div class="leading-tight">
                        <p class="text-xs uppercase tracking-[0.2em] text-sky-300">Dire Dawa City Administration</p>
                        <p class="text-sm sm:text-base font-semibold text-slate-50">Tax Information Management System</p>
                    </div>
                </div>

                <nav class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#features" class="text-slate-200 hover:text-sky-300 transition-colors">Features</a>
                    <a href="#roles" class="text-slate-200 hover:text-sky-300 transition-colors">User Roles</a>
                    <a href="#about" class="text-slate-200 hover:text-sky-300 transition-colors">About</a>
                    <a href="#contact" class="text-slate-200 hover:text-sky-300 transition-colors">Contact</a>
                </nav>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-sky-400/70 bg-sky-500/10 px-4 py-1.5 text-xs font-medium text-sky-100 shadow-sm hover:bg-sky-500/20 transition">
                                <span>Go to Dashboard</span>
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14m-7-7l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-1.5 text-xs font-medium text-slate-100 hover:bg-white/5 transition">
                                <span>Log in</span>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center gap-2 rounded-full bg-sky-500 px-4 py-1.5 text-xs font-semibold text-slate-950 shadow-lg shadow-sky-500/40 hover:bg-sky-400 transition">
                                    <span>Register as Taxpayer</span>
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <main class="relative z-10">
            <!-- Hero -->
            <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-10 pb-16 lg:pt-16 lg:pb-20 grid gap-10 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)] items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-200 ring-1 ring-emerald-500/40">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        <span>Modern, secure, and auditable tax management</span>
                    </div>
                    <div class="space-y-4">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight text-slate-50">
                            A trusted digital platform for<br class="hidden sm:block" />
                            <span class="bg-gradient-to-r from-sky-300 via-emerald-300 to-indigo-300 bg-clip-text text-transparent">tax administration in Dire Dawa</span>
                        </h1>
                        <p class="max-w-xl text-sm sm:text-base text-slate-300">
                            The Dire Dawa City Tax Information Management System centralizes taxpayer registration, assessments, payments,
                            and reporting in one secure platform built for government standards.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-sky-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-sky-500/40 hover:bg-sky-400 transition">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 9l3-3m0 0l3 3m-3-3v12" />
                                </svg>
                                <span>Access the system</span>
                            </a>
                        @endif
                        <a href="#roles" class="inline-flex items-center gap-2 text-xs font-medium text-slate-200 hover:text-sky-300 transition">
                            <span>View user roles</span>
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 12h14m-7-7l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <dl class="mt-6 grid grid-cols-2 gap-4 max-w-lg text-xs sm:text-sm text-slate-300">
                        <div class="space-y-1">
                            <dt class="font-semibold text-slate-100">Role-based access</dt>
                            <dd>Separate secure workspaces for administrators, taxpayers, interviewers, and cashiers.</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="font-semibold text-slate-100">Audit-ready records</dt>
                            <dd>Every payment is traceable with full history, receipts, and responsible officer.</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="font-semibold text-slate-100">Real-time insights</dt>
                            <dd>Dashboards and reports for informed revenue and compliance decisions.</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="font-semibold text-slate-100">Secure by design</dt>
                            <dd>Authentication, role-based permissions, and modern infrastructure.</dd>
                        </div>
                    </dl>
                </div>

                <!-- Hero illustration / stats -->
                <div class="relative">
                    <div class="absolute -inset-6 rounded-3xl bg-gradient-to-br from-sky-500/20 via-indigo-500/10 to-emerald-500/10 blur-2xl"></div>
                    <div class="relative rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl shadow-sky-900/40 p-6 sm:p-8 space-y-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium text-slate-300">Today's status</p>
                                <p class="mt-1 text-lg font-semibold text-slate-50">City revenue overview</p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-3 py-1 text-[10px] font-semibold text-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                Live system
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs sm:text-sm">
                            <div class="rounded-2xl bg-slate-900/90 border border-white/5 p-3 space-y-1">
                                <p class="text-slate-400">Registered taxpayers</p>
                                <p class="text-lg font-semibold text-slate-50">24,580+</p>
                                <p class="text-[11px] text-emerald-300">+320 this month</p>
                            </div>
                            <div class="rounded-2xl bg-slate-900/90 border border-white/5 p-3 space-y-1">
                                <p class="text-slate-400">Processed payments</p>
                                <p class="text-lg font-semibold text-slate-50">98.7M Br</p>
                                <p class="text-[11px] text-emerald-300">Reconciled & receipted</p>
                            </div>
                            <div class="rounded-2xl bg-slate-900/90 border border-white/5 p-3 space-y-1">
                                <p class="text-slate-400">Average processing time</p>
                                <p class="text-lg font-semibold text-slate-50">&lt; 2 min</p>
                                <p class="text-[11px] text-sky-300">From assessment to receipt</p>
                            </div>
                            <div class="rounded-2xl bg-slate-900/90 border border-white/5 p-3 space-y-1">
                                <p class="text-slate-400">System uptime</p>
                                <p class="text-lg font-semibold text-slate-50">99.9%</p>
                                <p class="text-[11px] text-slate-400">Monitored & secured</p>
                            </div>
                        </div>

                        <div class="mt-3 rounded-2xl border border-sky-500/40 bg-gradient-to-r from-sky-600/20 to-emerald-500/10 px-4 py-3 flex items-start gap-3 text-xs text-slate-100">
                            <div class="mt-0.5">
                                <svg class="h-4 w-4 text-sky-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <p>
                                This system is an official platform of the Dire Dawa City Administration. Unauthorized access is prohibited and may be subject to legal action.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section id="features" class="border-t border-white/5 bg-slate-950/60 py-12 sm:py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-8 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-semibold text-slate-50">Built for modern tax administration</h2>
                            <p class="mt-2 max-w-2xl text-sm text-slate-300">
                                From registration and assessment to payment and reporting, every step is streamlined, traceable, and secure.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <article class="group rounded-2xl border border-white/5 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/40 hover:border-sky-400/70 hover:-translate-y-1 transition duration-200">
                            <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/20 text-sky-300">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-50">End-to-end taxpayer lifecycle</h3>
                            <p class="mt-2 text-xs text-slate-300">
                                Register, update, and manage taxpayer records with unified profiles, account balances, and compliance history.
                            </p>
                        </article>

                        <article class="group rounded-2xl border border-white/5 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/40 hover:border-emerald-400/70 hover:-translate-y-1 transition duration-200">
                            <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-300">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6v12m6-6H6" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-50">Secure digital payments</h3>
                            <p class="mt-2 text-xs text-slate-300">
                                Cashiers process payments with validations, automatic balance updates, digital receipts, and refund workflows.
                            </p>
                        </article>

                        <article class="group rounded-2xl border border-white/5 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/40 hover:border-indigo-400/70 hover:-translate-y-1 transition duration-200">
                            <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-200">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-50">Insights and accountability</h3>
                            <p class="mt-2 text-xs text-slate-300">
                                Role-specific dashboards, reports, and processed-by audit trails help leadership monitor performance and integrity.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- User Roles -->
            <section id="roles" class="border-t border-white/5 bg-slate-950/80 py-12 sm:py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-semibold text-slate-50">Designed for every stakeholder</h2>
                            <p class="mt-2 max-w-2xl text-sm text-slate-300">
                                Each role gets a tailored workspace with tools and permissions aligned to their responsibilities.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <!-- Taxpayer -->
                        <article class="flex flex-col rounded-2xl border border-white/5 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/40">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-emerald-300">For citizens & businesses</p>
                                    <h3 class="text-sm font-semibold text-slate-50">Taxpayers</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-[10px] font-medium text-emerald-200">Self-service</span>
                            </div>
                            <p class="text-xs text-slate-300 mb-4">
                                View summaries, make payments, submit complaints, and stay informed with official news and updates.
                            </p>
                            <ul class="mb-4 space-y-1.5 text-[11px] text-slate-300">
                                <li>• Personalized dashboard</li>
                                <li>• Online payment and receipts</li>
                                <li>• Complaint and feedback channel</li>
                            </ul>
                            <div class="mt-auto flex flex-wrap gap-2">
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-1.5 text-[11px] font-semibold text-slate-950 hover:bg-emerald-400 transition">
                                        <span>Taxpayer login</span>
                                    </a>
                                @endif
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-400/70 px-3 py-1.5 text-[11px] font-medium text-emerald-100 hover:bg-emerald-500/10 transition">
                                        <span>Register</span>
                                    </a>
                                @endif
                            </div>
                        </article>

                        <!-- Admin -->
                        <article class="flex flex-col rounded-2xl border border-white/5 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/40">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-sky-300">For revenue leaders</p>
                                    <h3 class="text-sm font-semibold text-slate-50">Administrators</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-sky-500/15 px-2.5 py-0.5 text-[10px] font-medium text-sky-200">Control center</span>
                            </div>
                            <p class="text-xs text-slate-300 mb-4">
                                Manage taxpayer records, monitor payments, generate reports, and oversee complaints and news.
                            </p>
                            <ul class="mb-4 space-y-1.5 text-[11px] text-slate-300">
                                <li>• Governance dashboards</li>
                                <li>• Taxpayer and payment management</li>
                                <li>• Reporting and oversight tools</li>
                            </ul>
                            <div class="mt-auto flex flex-wrap gap-2">
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-500 px-3 py-1.5 text-[11px] font-semibold text-slate-950 hover:bg-sky-400 transition">
                                        <span>Admin login</span>
                                    </a>
                                @endif
                            </div>
                        </article>

                        <!-- Cashier -->
                        <article class="flex flex-col rounded-2xl border border-white/5 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/40">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-indigo-300">For payment officers</p>
                                    <h3 class="text-sm font-semibold text-slate-50">Cashiers</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-indigo-500/15 px-2.5 py-0.5 text-[10px] font-medium text-indigo-200">Frontline</span>
                            </div>
                            <p class="text-xs text-slate-300 mb-4">
                                Verify taxpayers, process payments, issue receipts, and handle refunds with full audit trails.
                            </p>
                            <ul class="mb-4 space-y-1.5 text-[11px] text-slate-300">
                                <li>• Fast taxpayer lookup</li>
                                <li>• Integrated payment and receipt flow</li>
                                <li>• Refund and adjustment support</li>
                            </ul>
                            <div class="mt-auto flex flex-wrap gap-2">
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-500 px-3 py-1.5 text-[11px] font-semibold text-slate-950 hover:bg-indigo-400 transition">
                                        <span>Cashier login</span>
                                    </a>
                                @endif
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- About -->
            <section id="about" class="border-t border-white/5 bg-slate-950/90 py-12 sm:py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid gap-8 md:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)] items-start">
                    <div class="space-y-3">
                        <h2 class="text-xl sm:text-2xl font-semibold text-slate-50">About the system</h2>
                        <p class="text-sm text-slate-300">
                            The Dire Dawa City Tax Information Management System was developed to modernize how the city manages
                            its tax base, improves revenue collection, and serves citizens and businesses with transparency.
                        </p>
                        <p class="text-sm text-slate-300">
                            By digitizing taxpayer data, integrating payment channels, and providing clear audit trails, the
                            system supports evidence-based decision-making and helps combat revenue leakage.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 text-xs text-slate-200">
                        <div class="rounded-2xl border border-white/5 bg-slate-900/80 p-4 space-y-1.5">
                            <p class="text-[11px] font-semibold text-sky-200">Governance</p>
                            <p class="text-sm font-semibold text-slate-50">Compliance & integrity</p>
                            <p class="text-slate-300">Every change is traceable with responsible users and timestamps.</p>
                        </div>
                        <div class="rounded-2xl border border-white/5 bg-slate-900/80 p-4 space-y-1.5">
                            <p class="text-[11px] font-semibold text-emerald-200">Citizens first</p>
                            <p class="text-sm font-semibold text-slate-50">Simplified experience</p>
                            <p class="text-slate-300">Clear interfaces, online access, and consistent communication.</p>
                        </div>
                        <div class="rounded-2xl border border-white/5 bg-slate-900/80 p-4 space-y-1.5">
                            <p class="text-[11px] font-semibold text-indigo-200">Security</p>
                            <p class="text-sm font-semibold text-slate-50">Protected information</p>
                            <p class="text-slate-300">Authentication, role-based access, and encrypted connections.</p>
                        </div>
                        <div class="rounded-2xl border border-white/5 bg-slate-900/80 p-4 space-y-1.5">
                            <p class="text-[11px] font-semibold text-amber-200">Scalability</p>
                            <p class="text-sm font-semibold text-slate-50">Ready for growth</p>
                            <p class="text-slate-300">Built to evolve with new services, reports, and integrations.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact / Footer -->
            <footer id="contact" class="border-t border-white/10 bg-slate-950 py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between text-xs text-slate-300">
                    <div>
                        <p class="font-semibold text-slate-100">Dire Dawa City Tax Information Management System</p>
                        <p class="mt-1 text-slate-400">Official platform of the Dire Dawa City Administration Revenue Authority.</p>
                    </div>
                    <div class="space-y-1">
                        <p><span class="font-medium text-slate-100">Support:</span> <span class="text-slate-300">support@example.gov.et</span></p>
                        <p><span class="font-medium text-slate-100">Office hours:</span> <span class="text-slate-300">Mon - Fri, 8:30-12:30 & 2:00-5:30</span></p>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</body>
</html>
