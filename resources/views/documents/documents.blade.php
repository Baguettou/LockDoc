@extends('layouts.app')

@section('content')


<h1>Subir Documento</h1>
<form action="subir.php" method="post" enctype="multipart/form-data">
    <input type="file" name="documento">
    <input type="submit" value="Subir">
</form>

@endsection