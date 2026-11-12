@foreach($keyword->images->shuffle() as $image)
    @if($loop->first)
        <p>Jika {kamu|anda|ente} {sedang|lagi|mau} {mencari|melihat} jawaban {dari|atas|mengenai} {soal|pertanyaan} {!! $answer->question !!} {kamu|anda|ente} berada di {halaman|tempat|website} yang {tepat|benar}.
            Kami {punya|mempunyai|ada} {{ count($answer->answers) }} jawaban {dari|atas|mengenai} {!! $answer->question !!}. Silakan {baca|pelajari|lihat} jawaban {lebih lanjut|selanjutnya|selengkapnya} {disini|di bawah|di bawah ini}:</p>

        <img alt="{{ $image->title }}" src="{{ $image->url }}" width="100%" onerror="this.onerror=null;this.src='{{ $image->thumbnail }}';" />

        <h2>{!! ucwords($answer->question) !!}</h2>

        @foreach($answer->answers as $key => $answer)
            <strong>Jawaban: #{{ $key+1 }}:</strong>
            {!! $answer !!}
        @endforeach
    @endif

    <img alt="{{ $image->title }}" src="{{ $image->url }}" width="100%" onerror="this.onerror=null;this.src='{{ $image->thumbnail }}';" />

    <small>{{ $image->domain }}</small>
    <p>{{ $image->desc }}</p>
@endforeach

<p>{{ $keyword->sentences(3) }}</p>
