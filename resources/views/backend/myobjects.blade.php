@extends('layouts.backend')

@section('content')
<h2 style="margin-top: 80px;">Список объектов</h2>
@foreach( $objects as $object )

<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">{{ $object->name  }} <small><a href="{{ route('saveObject',['id'=>$object->id]) }}" class="btn btn-danger btn-xs">редактировать</a> <a href="{{ route('saveRoom').'?object_id='.$object->id }}" class="btn btn-danger btn-xs">добавить номер</a> <a title="delete" href="{{ route('deleteObject',['id'=>$object->id]) }}"><span class="glyphicon glyphicon-remove"></span></a></small> </h3>
    </div>

    <div class="panel-body">
        @foreach( $object->rooms as $room )
        <span class="my_objects">
            Номер {{ $room->room_number }} <a title="edit" href="{{ route('saveRoom',['id'=>$room->id]) }}"><span class="glyphicon glyphicon-edit"></span></a> <a title="delete" href="{{ route('deleteRoom',['id'=>$room->id]) }}"><span class="glyphicon glyphicon-remove"></span></a> </span>
        @endforeach
    </div>

</div>

@endforeach
@endsection