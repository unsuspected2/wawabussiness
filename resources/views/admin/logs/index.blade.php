@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="text-success fw-bold mb-4">Auditoria de Sistema (Logs)</h2>

    <div class="card bg-dark border-secondary shadow">
        <div class="card-body p-0">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Descrição</th>
                        <th>Data/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->causer->name ?? 'Sistema' }}</td>
                        <td>
                            <span class="badge {{ $log->event == 'deleted' ? 'bg-danger' : ($log->event == 'created' ? 'bg-success' : 'bg-primary') }}">
                                {{ strtoupper($log->event) }}
                            </span>
                        </td>
                        <td>
                            O usuário {{ $log->event }} um registro em
                            <strong>{{ str_replace('App\\Models\\', '', $log->subject_type) }}</strong>
                        </td>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
