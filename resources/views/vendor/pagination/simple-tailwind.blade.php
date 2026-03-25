@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary-600 bg-white border border-secondary-200 cursor-not-allowed leading-5 rounded-md dark:text-secondary-300 dark:bg-secondary-700 dark:border-secondary-600">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary-800 bg-white border border-secondary-200 leading-5 rounded-md hover:text-secondary-700 focus:outline-none focus:ring ring-secondary-200 focus:border-primary-300 active:bg-secondary-100 active:text-secondary-800 transition ease-in-out duration-150 dark:bg-secondary-800 dark:border-secondary-600 dark:text-secondary-200 dark:focus:border-primary-700 dark:active:bg-secondary-700 dark:active:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-900 dark:hover:text-secondary-200">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary-800 bg-white border border-secondary-200 leading-5 rounded-md hover:text-secondary-700 focus:outline-none focus:ring ring-secondary-200 focus:border-primary-300 active:bg-secondary-100 active:text-secondary-800 transition ease-in-out duration-150 dark:bg-secondary-800 dark:border-secondary-600 dark:text-secondary-200 dark:focus:border-primary-700 dark:active:bg-secondary-700 dark:active:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-900 dark:hover:text-secondary-200">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary-600 bg-white border border-secondary-200 cursor-not-allowed leading-5 rounded-md dark:text-secondary-300 dark:bg-secondary-700 dark:border-secondary-600">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif
