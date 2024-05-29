<div class="modal-header p-3">
    <h5 class="modal-title">{{ ucFirst($team->name) }} <span></span></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body p-3" style="max-height: 400px; overflow: scroll">

    <table class="table table-hover">
        <thead>
            <th class="p-2">Name</th>
            <th class="p-2">Email</th>
        </thead>
        <tbody>
            @forelse ($team->users as $user)
                <tr>
                    <td class="p-2">{{$user->name ?? '' }}</td>
                    <td class="p-2">{{$user->email ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">No Data Available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>