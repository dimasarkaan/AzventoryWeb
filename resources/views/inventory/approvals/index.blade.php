<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.approvals_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.approvals_desc') }}</p>
                </div>
            </div>

            <!-- Mobile Card View (Refined) -->
            <div class="md:hidden space-y-4">
                @forelse ($pendingApprovals as $approval)
                    <x-approval.card :approval="$approval" />
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm">{{ __('ui.no_pending') }}</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>{{ __('ui.item_column') }}</th>
                                <th>{{ __('ui.applicant_column') }}</th>
                                <th>{{ __('ui.type_column') }}</th>
                                <th>{{ __('ui.amount_column') }}</th>
                                <th>{{ __('ui.reason_column') }}</th>
                                <th>{{ __('ui.date_column') }}</th>
                                <th class="text-right">{{ __('ui.action_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingApprovals as $approval)
                                <x-approval.table-row :approval="$approval" />
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-success-100 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>{{ __('ui.no_pending_approvals') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="mt-6">
                {{ $pendingApprovals->links() }}
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function confirmReject(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: '{{ __('ui.confirm_reject_title') }}',
                text: "{{ __('ui.confirm_reject_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.btn_yes_reject') }}',
                cancelButtonText: '{{ __('ui.btn_cancel') }}',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200',
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: '#ef4444',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
