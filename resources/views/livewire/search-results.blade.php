<div class="{{$show ? 'block' : 'hidden'}}">
        <div class="mt-4 p-4 absolute border rounded-md bg-gray-700 border-indigo-500">

            <div class="absolute right-0 top-0 pt-2 pr-2">
                <button type="button"
{{--                    wire:click="dispatch('search:clear')"--}}
                >X</button>
            </div>
            @if(count($results) === 0)
                <p>No results found !</p>
            @endif

            @foreach($results as $result)
                <div class="pt-2" wire:key="{{$result->id}}">
                    <a wire:navigate.hover href="/articles/{{$result->id}}">{{$result->title}}</a>
                </div>
            @endforeach
        </div>
</div>
