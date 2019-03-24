<div class="app-sidebar {{$more_classes}}">
    <nav class="nav mt-4 flex-column">
        <a href="{{url('ruang')}}" class="nav-link{{$active_link == url('ruang')? " active " : " "}}">Ruang</a>
        <a href="{{url('jenis')}}" class="nav-link{{$active_link == url('jenis')? " active " : " "}}">Jenis</a>
        <a href="{{url('inventaris')}}" class="nav-link{{$active_link == url('inventaris')? " active " : " "}}">Inventaris</a>
    </nav>
</div>