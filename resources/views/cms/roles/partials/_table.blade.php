<div class="table-responsive bg-white">
    <table class="table table-rounded text-center" id="role_table">
        <thead class="bg-brand thead-brand">
        <tr>
            <th>Role name</th>
            @canany('permissions_to_role','role_edit','role_delete')
                <th>Actions</th>
            @endcanany
        </tr>
        </thead>
        <tbody class="tbody-gray">
        @if ($items->total())
            @foreach($items as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    @canany('add_permissions_to_role','role_edit','role_delete')
                        <td>
                            @can('add_permissions_to_role')
                                <a href="{{ route('roles.permissions',$item->id) }}" title="Permissions"
                                   class="btn btn-primary"><i class="fa fa-shield"></i></a>
                            @endcan
                            @can('role_edit')
                                <a href="{{ route('roles.edit',$item->id) }}" class="btn btn-success"><i
                                        class="fa fa-edit"></i></a>
                            @endcan
                            @can('role_delete')
                                <button data-action="{{ route('roles.delete',$item->id) }}"
                                        class="btn btn-danger delete_role"><i class="fa fa-trash"></i></button>
                            @endcan
                        </td>
                    @endcanany
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4">No data available</td>
            </tr>
        @endif
        </tbody>
    </table>
    {{$items->appends($_GET)->links()}}
</div>
