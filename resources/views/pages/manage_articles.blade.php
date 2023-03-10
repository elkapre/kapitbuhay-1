@extends('layouts.index')

<!-- Page Title -->
@section('title', 'Articles')

<!-- Styles -->
@section('styles')
<style>
 .search {
    margin-top: 100px;
 }

 .bill-header.cs {
  background-color: rgba(37,71,106,0.56);
  color: #fff;
}

.modal-footer-text-center {
    text-align: center;
    margin-bottom: 1%;
}

.container-text-center {
    height: 350px;
    width: 430px;
    margin-left: 520px;
    align-items: center;
    justify-content: center;
}

.container-text-center .wrapper {
    position: relative;
    height: 200px;
    width: 75%;
    border-radius: 10px;
    background: #fff;
    border: 2px dashed #c2cdda;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.wrapper .active {
    border: none;
}

.wrapper .image{
    position: absolute;
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wrapper img {
    height: 100%;
    width: 100%;
    object-fit: cover;

}

.wrapper .text {
    font-size: 20px;
    font-weight: 500;
    color: #5B5B7B;
    text-align: center;
    margin-bottom: 130px;
    margin-left: 20px;
}
.wrapper #cancel-bbtn {
    position: absolute;
    font-size: 20px;
    color: #9658fe;
    cursor: pointer;
    display: none;
    right: 15px;
    top: 15px;

}

.wrapper.active:hover #cancel-btn i {
    display: none;
}

.wrapper #cancel-btn i:hover {
    color: #e74c3c;
}

.wrapper .file-name {
    position: absolute;
    bottom: 0px;
    width: 100%;
    padding: 8px 0;
    font-size: 18px;
    color: #fff;
    display: none;
    background: #e74c3c;
}

.wrapper.active:hover .file-name {
    display: block;
}

.container-text-center #custom-btn {
    margin-top: 10px;
    display: block;
    width: 50%;
    height: 50px;
    border: none;
    outline: none;
    border-radius: 25px;
    color: #fff;
    font-size: 18px;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    background: #e74c3c;
    margin-left: 60px;
}




</style>

@stop

<!-- Content -->
@section('content')
<div class="container search" style="overflow-y: scroll; overflow-x: hidden; height:600px;">
    <div class="row g-1">
        <div class="col-md-5">
            <input class= "container-fluid h-100" type="text" placeholder="Search">
        </div>
        <div class="col-md-6 ">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Create New Article
        </button>
    </div>

<div class="form-group mt-5">
    <table class="table table-hover table-bordered text-center">
                <thead class="bill-header cs">
                    <tr>
                        <th id="trs-hd-1" class="col-lg-2">ID</th>
                        <th id="trs-hd-2" class="col-lg-2">Article Name</th>
                        <th id="trs-hd-3" class="col-lg-1">Date Created</th>
                        <th id="trs-hd-4" class="col-lg-2"></th>
                        <th id="trs-hd-5" class="col-lg-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($document == null)
                    <tr>
                        <td class="text-center" colspan="3">No Data!</td>
                    </tr>
                    @else
                        @foreach ($document as $item)
                            <tr>
                                <td>{{ $item->id() }}</td>
                                <td>{{ $item['article_title'] }}</td>
                                <td> Sample</td>
                                <td>
                                    <!-- Modal trigger button -->
                                    <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop2{{ $item->id() }}">Update</button>

                                </td>
                                <td>
                                    <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop3{{ $item->id() }}">Delete</button>
                                </td>

                            </tr>
                        @endforeach
                    @endif
                </tbody>
    </table>
</div>
@if ($document == null)
@else
    @foreach ($document as $item)
        <div class="modal fade" id="staticBackdrop2{{ $item->id() }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <form action="article/{{ $item->id() }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                            <h3 class="modal-title position-absolute top-30 start-50 translate-middle" id="staticBackdropLabel">Update Article {{ $item->id() }}</h3>
                        </div>
                        <div class="modal-body">
                            <div class="container-text-center">
                                <div class="wrapper">
                                    <div class="image">
                                        <img src="{{ $item['image'] }}" alt="Article Image">
                                    </div>
                                    <div class="content">
                                        <div class="text">
                                            Attach Image
                                        </div>
                                    </div>
                                    <div id="cancel-btn">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="file-name">
                                        File name here
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <input name="articleimage" id="myFileArticleImage" type="file" class="form-control align-content-center w-120">
                                </div>
                            </div>
                            <div class="container border-secondary" style="margin-top:0%; margin-bottom:0%;">
                                <div class="row">
                                    <div class="col-md-2 mb-2 text-start fw-bold">
                                        Article Title:
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-4 "><input name="updateArticleTitle" id="updateArticleTitle" type="text" class="form-control align-content-center w-100"  placeholder="Title" value="{{ $item['article_title'] }}"></div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-2 text-start">Article Contents: </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-mb-3">  <textarea class="form-control" name="updateArticleContent" id="updateArticleContent" rows="3">{{ $item['article_content'] }}</textarea></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer-text-center">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endforeach
@endif
@if ($document == null)
@else
        @foreach ($document as $item)
            <div class="modal fade" id="staticBackdrop3{{ $item->id() }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel"></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            Are you sure you want to delete this file?
                        </div>
                        <div class="modal-footer">
                            <form action="article/{{ $item->id() }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                <button type="submit" class="btn btn-primary">Yes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
@endif
    </td>
    </tr>
    </tbody>
    </div>
</div>

<form action="article" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <h3 class="modal-title position-absolute top-25 start-50 translate-middle" id="staticBackdropLabel">Create New Article</h3>
                </div>
                <div class="modal-body">
                    <div class="container-text-center">
                        <div class="wrapper">
                            <div class="image">
                                <img src="ball.jpg" alt="Ball">
                            </div>
                        <div class="content">
                         <div class="text">
                            Attach Image
                        </div>
                    </div>
                    <div id="cancel-btn">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="file-name">
                        File name here
                    </div>
                </div>
                <div class="col-md-8">
                    <input name="articleimage" id="myFileArticleImage" type="file" class="form-control align-content-center w-120" required>
                </div>
            </div>
            <div class="container border-secondary" style="height:400px; margin-top:0%; margin-bottom:0%;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        Article Title:
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-4 ">
                        <input name="createArticleTitle" id="createArticleTitle" type="text" class="form-control align-content-center w-100"  placeholder="Title" >
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">Add in: </div>
                </div>
                <div class="row mt-2 ">
                    <div class="col-md-3">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Coping Mechanism Article
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-8">Article Contents: </div>
                </div>
                <div class="row mt-3">
                    <div class="col-mb-3">  <textarea class="form-control" name="createArticleContent" id="createArticleContent" rows="3"></textarea></div>
                </div>
            </div>

        </div>
        <div class="modal-footer-text-center">
            <button type="submit" class="btn btn-primary"> Save </button>
        </div>
    </div>
</form>


@stop

<!-- Scripts -->
@section('scripts')
<script>


</script>



@stop
