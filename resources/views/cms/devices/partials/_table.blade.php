<div class="table-responsive bg-white">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="devices_table">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Project</th>
            <th class="min-w-125px">Machine</th>
            <th class="min-w-125px">Process</th>
            <th class="min-w-125px">Version</th>
            <th class="min-w-125px">Timezone</th>
            <th class="min-w-125px">Address</th>
            <th class="min-w-125px">Status</th>
            <th class="min-w-125px">Created Date</th>
            @canany('device_edit','device_delete')
                <th class="text-end min-w-100px">Actions</th>
            @endcanany
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @if ($items->total())
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->project }}</td>
                    <td>{{ $item->machine }}</td>
                    <td>{{ $item->process }}</td>
                    <td>{{ $item->version }}</td>
                    <td>{{ $item->timezone }}</td>
                    <td>{{ ($item->country->name??'-') . ' - ' . ($item->city->name??'-') }}</td>
                    <td>{{ \App\Enums\Constants::getNameById($item->status) }}</td>
                    <td>{{ $item->created_at->diffForHumans() }}</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                           data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                            <span class="svg-icon svg-icon-5 m-0">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('devices.edit',1) }}" class="menu-link px-3">Edit</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-kt-devices-table-filter="delete_row">Delete</a>
                            </div>
                        </div>
                    </td>
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


