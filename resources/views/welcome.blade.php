@extends('layouts.app', ['page' => __('Shortenert'), 'pageSlug' => 'Shortenert'])


@section('content')
<style>
  .row > a {
    width: 100%;
  }
  .search-inline { 
    height: 100%;
    align-self: center;
  }
  .base-align {
    vertical-align: sub;
  }
</style>
<div class="row">
  <div class="col-md-6 offset-md-3">
    
    <div class="card">
      <div class="card-header">
      <label> Get Link Shortened </label>
      </div>
      
      <div class="card-body">
      
        <input type="text" id='inputLink' name="link" class="form-control" placeholder="{{ ('Link to be shorteen') }}" required>
        <button class="btn btn-primary btn-block" onclick='_getLink()'>Proccess</button>

      </div> 
      <div class='card-footer'>
        <div id='linkShortened'> </div>
      </div>

    </div>
  </div>

  <div class="col-md-6 offset-md-3">
    
    <div class="card">
      
      <div class="card-header">
        <label>Click to show Most accessed Links Stored</label>
      </div>
      
      <div class="card-body">
      
        <button class="btn btn-primary btn-block" onclick='_getMostLinks();'>SHOW Links</button>
        <ul id='mostLinkAnchor'> </ul>

      </div> 

    </div>
  </div>

  <div class="col-md-6 offset-md-3">
    
    <div class="card">
      
      <div class="card-header">
        <label> Go To Link Shorten</label>
      </div>
      
      <div class="card-body">
        <input type="text" id='uidField' name="uid" class="form-control" placeholder="{{ ('uid') }}">
        <button class="btn btn-primary btn-block" onclick='_getLinkShortenByUid();'>Go web</button>
      </div> 

    </div>
  </div>

</div>
@endsection
@push('js')
<script type='text/javascript'>
  
  async function _getLink() {
    let link = $("#inputLink").val();
    console.log(link)

    if (!link) {
      return false;
    }
		
    await post("{{ route('write.link') }}", {link}, (result) => {		
      
      if (!result || !result.success || !result.data.link)
        console.error('error');

      let anchor = document.getElementById("linkShortened");
      let a = document.createElement('a');
      var linkText = document.createTextNode(`Here is your Link`);
      a.appendChild(linkText);
      a.title = "Here is your shortten link :: localhost://${result.data.link}"
      a.href = result.data.link;

      anchor.appendChild(a);
		});

  }

  async function _getLinkShortenByUid() {
    
    let uid = $("#uidField").val();
    if (!uid)
      return;

    await get(`{{ url('shortener/${uid}') }}`, null, (result) => {

      if (!result.data.linkOfUid)
        return console.error('error');

      window.open(result.data.linkOfUid, '_blank');
    });
  }

  async function _getMostLinks() {

    await get(`{{ route('get.links') }}`, null, (result) => {

      if (!result || !result.data)
        return console.error('error');


      let data = result.data;
      let ul = document.getElementById('mostLinkAnchor');
{{-- console.log(result) --}}

      for (let [id, val] of Object.entries(data.links)) {
        console.log(id, val)
        let li = document.createElement('li');
        li.innerHTML = `${id+1} : ${val}`

        ul.appendChild(li);

      }

      
    });
  }

</script>
@endpush
