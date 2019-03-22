@extends('layouts.app') 
@section('content') @component('components.sidebar', [ 'more_classes' => 'col-12 col-md-3 col-xl-2',
'active_link' => url('home')]) @endcomponent

<div class="col-12 col-md-9 col-xl-10 pt-4">
    <div class="container">
        <h1>Dashboard</h1>
        <h4>Here's a subtitle</h4>
        <div class="alert alert-warning">
            <h4>Ay!</h4>
            <p>Here's some <b>alert</b> you'll be facing probably</p>
        </div>
        <div class="alert alert-danger">
            <h4>Warning!</h4>
            <p>This alert looks more <b>important</b> You should pay attention</p>
        </div>
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <span>Sample Text</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3>Title</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nisi maiores eveniet magnam voluptatem perspiciatis
                            tenetur possimus dolorum voluptatibus, dignissimos voluptatum quaerat praesentium corporis animi
                            deserunt itaque laudantium corrupti. Culpa, corporis?</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <div class="card-title">
                            <span>Sample Control</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="textfield">Label</label>
                            <input type="text" name="textfield" id="textfield" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputGroup">Input Groups</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        @
                                    </span>
                                </div>
                                <input type="text" name="inputGroup" id="inputGroup" class="form-control">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Rp
                                    </span>
                                </div>
                                <input type="text" name="inputGroup" id="inputGroup" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                            ,00
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection