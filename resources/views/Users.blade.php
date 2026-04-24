@extends('template')

@section('main')
<div class="container mt-4">
    <h2 class="mb-4">Gestion des Utilisateurs</h2>

    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">Ajouter un utilisateur</div>
        <div class="card-body">
            <form action="{{ route('web.users.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Nom" required></div>
                    <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                    <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Mot de passe" required></div>
                    <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Créer</button></div>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-striped border">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}">Modifier</button>

                        <form action="{{ route('web.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger ms-1">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>

            <div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('web.users.update', $user->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Modifier {{ $user->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Nom</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
@endsection
