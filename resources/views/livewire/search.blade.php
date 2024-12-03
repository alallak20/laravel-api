<div>
    <form>
        <div class="mt-2">
                <input
                    type="text"
                    class=" w-full border rounded-md bg-gray-700 text-white"
                    wire:model.live.debounce="searchText"
                    placeholder= '{{$placeholder ?? "Search stuff..."}}'

                >
{{--            <button--}}
{{--                class="text-white rounded-md font-medium p-4 disabled:bg-indigo-400 bg-indigo-600"--}}
{{--                wire:click.prevent="clear()"--}}
{{--                {{empty($searchText) ? 'disabled' : ''}}--}}
{{--            >Clear</button>--}}
        </div>
    </form>

    <livewire:search-results :results='$results' :show="!empty($searchText)"></livewire:search-results>
</div>
