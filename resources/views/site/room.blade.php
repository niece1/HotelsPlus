@extends('layouts.app')

@section('content')
<div class="room places">
    <div class="room-wrapper">
        <h1 class="text-center">Номер в отеле <a href="{{ route('object',['room'=>$room->object_id]) }}" style="text-decoration: none;">{{ $room->object->name  }}</a></h1>

        @foreach( $room->shots->chunk(3) as $chunked_shots )
        <div class="row top-buffer">

            @foreach($chunked_shots as $shot)

            <div class="col-md-4">
                <img class="img-responsive" style="margin-bottom: 5px;" src="{{ $shot->path ?? $placeholder  }}" alt="Photo">
            </div>

            @endforeach

        </div>

        @endforeach

        <section>

            <ul class="list-group">
                <li class="list-group-item">
                    <span class="bolded" style="color: #ee2852">Описание:</span> {{ $room->description }}
                </li>
                <li class="list-group-item">
                    <span class="bolded" style="color: #ee2852">Количество гостей:</span> {{ $room->room_size }}
                </li>
                <li class="list-group-item">
                    <span class="bolded" style="color: #ee2852">Цена за ночь:</span> {{ $room->price }} USD
                </li>
                <li class="list-group-item">
                    <span class="bolded" style="color: #ee2852">Адрес:</span> {{ $room->object->city->name }} {{ $room->object->address->street }} {{ $room->object->address->number }}
                </li>
            </ul>
        </section>

        <section id="reservation">

            <h3 style="color: #ee2852">Бронирование</h3>

            <div class="row">
                <div class="col-md-6">
                    <form method="POST" {{ $novalidate }} action="{{ route('makeReservation',['room_id'=>$room->id,'city_id'=>$room->object->city->id]) }}">
                        <div class="form-group">
                            <label for="checkin">Заезд</label>
                            <input required name="checkin" type="text" class="form-control datepicker" id="checkin" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="checkout">Выезд</label>
                            <input required name="checkout" type="text" class="form-control datepicker" id="checkout" placeholder="">
                        </div>
                        @if(Auth::guest())
                        <p><a href="{{ route('login') }}">Залогиньтесь, чтобы зарезервировать</a></p>
                        @else
                        <button type="submit" class="btn btn-primary">Зарезервировать</button>
                        @endif
                        <p class="text-danger" style="margin-top: 5px;">{{ Session::get('reservationMsg') }}</p>
                        @csrf
                    </form>
                </div><br>
                <div class="col-md-6">
                    <div id="avaiability_calendar"></div>
                </div>
            </div>

        </section>
    </div>
</div>
@endsection

@push('scripts')

<script>
    function datesBetween(startDt, endDt) {
        var between = [];
        var currentDate = new Date(startDt);
        var end = new Date(endDt);
        while (currentDate <= end) {
            between.push($.datepicker.formatDate('mm/dd/yy', new Date(currentDate)));
            currentDate.setDate(currentDate.getDate() + 1);
        }

        return between;
    }

    $.ajax({

        cache: false,
        url: base_url + '/ajaxGetRoomReservations/' + {
            {
                $room - > id
            }
        },
        type: "GET",
        success: function(response) {


            var eventDates = {};
            var dates = [];

            for (var i = 0; i <= response.reservations.length - 1; i++) {
                dates.push(datesBetween(new Date(response.reservations[i].day_in), new Date(response.reservations[i].day_out))); // array of arrays
            }


            /*  a = [1];
                b = [2];
                x = a.concat(b);
                x = [1,2];
                [ [1],[2],[3] ] => [1,2,3]  */
            dates = [].concat.apply([], dates); // flattened array

            for (var i = 0; i <= dates.length - 1; i++) {
                eventDates[dates[i]] = dates[i];
            }


            $(function() {
                $("#avaiability_calendar").datepicker({
                    onSelect: function(data) {

                        //            console.log($('#checkin').val());

                        if ($('#checkin').val() == '') {
                            $('#checkin').val(data);
                        } else if ($('#checkout').val() == '') {
                            $('#checkout').val(data);
                        } else if ($('#checkout').val() != '') {
                            $('#checkin').val(data);
                            $('#checkout').val('');
                        }

                    },
                    beforeShowDay: function(date) {
                        var tmp = eventDates[$.datepicker.formatDate('mm/dd/yy', date)];
                        //console.log(date);
                        if (tmp)
                            return [false, 'unavaiable_date'];
                        else
                            return [true, ''];
                    }

                });
            });

        }

    });
</script>

@endpush