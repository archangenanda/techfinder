@extends('template')

@section('main')
<div class="container mt-4">
    <h2 class="mb-4">Gestion des Compétences - TechFinder</h2>


    <div class="card mb-5 border-primary">
        <div class="card-header bg-primary text-white">Ajouter une nouvelle compétence</div>
        <div class="card-body">
            <form action="{{ route('competences.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Libellé</label>
                        <input type="text" name="label_comp" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description_comp" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-striped table-hover border">
        <thead class="table-dark">
            <tr>
                <th>Code</th>
                <th>Label</th>
                <th>Description</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($Competence_list as $competence)
                <tr>
                    <td>{{ $competence->code_comp }}</td>
                    <td>{{ $competence->label_comp }}</td>
                    <td>{{ $competence->description_comp }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $competence->code_comp }}">
                                Modifier
                            </button>

                            <form action="{{ route('competences.destroy', $competence->code_comp) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger ms-1">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <div class="modal fade" id="editModal{{ $competence->code_comp }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Modifier : {{ $competence->label_comp }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('competences.update', $competence->code_comp) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Libellé</label>
                                        <input type="text" name="label_comp" class="form-control" value="{{ $competence->label_comp }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description_comp" class="form-control" required>{{ $competence->description_comp }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifs</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-4">
    {{ $Competence_list->links() }}
</div>
</div>
@endsection
