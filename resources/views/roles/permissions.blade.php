<div class="space-y-5">
    @if ($permissions->isEmpty())
        <p class="text-sm text-stone-500">No permissions exist yet. <a href="{{ route('permissions.create') }}" class="link">Create one</a>.</p>
    @else
        @php
            $groups = [
                'Catalog' => \App\Support\Permissions::catalog(),
                'Orders' => \App\Support\Permissions::orders(),
                'Access control' => \App\Support\Permissions::access(),
            ];
            $selectedIds = collect($selectedPermissions)->map(fn ($id) => (int) $id);
        @endphp
        @foreach ($groups as $label => $permissionNames)
            @php
                $groupPermissions = $permissions->whereIn('name', $permissionNames);
            @endphp
            @unless ($groupPermissions->isEmpty())
                <fieldset class="fieldset">
                    <legend class="legend">{{ $label }}</legend>
                    <div class="space-y-2">
                        @foreach ($groupPermissions as $permission)
                            <label class="flex items-center gap-2 text-sm text-stone-700">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="checkbox"
                                       {{ $selectedIds->contains((int) $permission->id) ? 'checked' : '' }}>
                                {{ $permission->name }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endunless
        @endforeach
    @endif
</div>