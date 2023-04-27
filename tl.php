<?php
# create a folder in tl called pt for pure trance, for example
# in this folder, create files called tl_pt_001.txt for example
$qs = $_SERVER["QUERY_STRING"];
$tlstub = "tl_${qs}";
$tldir = "tl/$qs";
if( ! preg_match("@^[a-z0-9]*$@",$qs) || ! is_dir( $tldir ) ) {
  echo "tl dir $qs does not exist\n";
  exit();
}
$epfiles = glob("$tldir/${tlstub}_*.txt");
$eplist = [];
$a = [];
foreach($epfiles as $f) {
  preg_match("@${tlstub}_(.*)\\.txt@",$f,$m);
  array_push($a,[$m[1],$f]);
}
$j = json_encode($a);
?><!DOCTYPE html>
<html>
<head>
<meta charset='utf8'/>
<title>Tracklists</title>
<link rel="icon" href="tracklist-icon.png"/>
<script>
const list = <?php echo $j; ?>;
const tl_name = "<?php echo $qs; ?>"
</script>
<script>
const q = (x,y=document) => y.querySelector(x)
const qq = (x,y=document) => Array.from(y.querySelectorAll(x))
let ep
const d = new Map()
const dl = [] // i -> h,t,u,v
const dlm = new Map() // v -> i
let dli = 0
let ch,ct,cu
let nav, navh, navt, naveps, tl
const getUrl = url => {
    var Httpreq = new XMLHttpRequest()
    Httpreq.open("GET",url,false)
    Httpreq.send(null)
    return Httpreq.responseText
}
d.assign = function(h,t,u,v) {
  if( !this.has(h) ) { this.set(h,new Map()) }
  if( !this.get(h).has(t) ) { this.get(h).set(t,new Map()) }
  this.get(h).get(t).set(u,v)
}
const upd_hs = _ => {
  const div = q("div.nav .hundreds")
  div.innerHTML = ""
  const hs = Array.from(d.keys())
  hs.forEach(h => {
    const e = document.createElement("span")
    e.innerText = h
    e.classList.add("selector","hundred")
    e.addEventListener("click",hundreds_click)
    div.append(e)
  })
}
const upd_ts = _ => {
  const div = q("div.nav .tens")
  div.innerHTML = ""
  const ts = Array.from(d.get(ch).keys())
  ts.forEach(t => {
    const e = document.createElement("span")
    e.innerText = t
    e.classList.add("selector","ten")
    e.addEventListener("click",tens_click)
    div.append(e)
  })
}
const upd_us = _ => {
  const div = q("div.nav2 span.episodes")
  div.innerHTML = ""
  const us = Array.from(d.get(ch).get(ct).keys())
  us.forEach(u => {
    const e = document.createElement("span")
    e.innerText = u
    e.classList.add("selector","unit")
    e.addEventListener("click",units_click)
    div.append(e)
  })
}
const hundreds_click = e => {
  const h = e.target.innerText
  sel_h(h)
}
const tens_click = e => {
  const t = e.target.innerText
  sel_t(t)
}
const units_click = e => {
  const u = e.target.innerText
  sel_u(u)
}
const sel_h = h => {
  if(! d.has(h) ) {
    console.log(`Episode hundreds = ${h} not in list`)
    return false
  }
  ch = h
  sel_t()
  upd_ts()
  return true
}
const sel_t = t => {
  if( t === undefined ) {
    if( ! d.has(ch) ) {
      console.log(`Episode hundreds ${ch} does not exist`)
      return false
    }
    const ts = d.get(ch)
    if( ts.size === 0 ) {
      console.log(`No tens in ${ch} does not exist`)
      return false
    }
    const ks = Array.from(ts.keys())
    t = ks[0]
  }
  if( ! d.has(ch) || ! d.get(ch).has(t) ) {
    console.log(`Episode tens = ${t} not in list`)
    return false
  }
  ct = t
  sel_u()
  upd_us()
  return true
}
const sel_u = u => {
  console.log(`sel ${ch} ${ct} ${u}`)
  console.log({t:d.get(ch).get(ct)})
  console.log({u:d.get(ch).get(ct).get(u)})
  if( u === undefined ) {
    console.log(`u ${u} undefined handler`)
    if( ! d.has(ch) ) {
      console.log(`Episode hundreds ${ch} does not exist`)
      return false
    }
    if( ! d.get(ch).has(ct) ) {
      console.log(`Episode tens ${ch} ${ct} does not exist`)
      return false
    }
    const us = d.get(ch).get(ct)
    if( us.size === 0 ) {
      console.log(`No episodes in ${ch} ${ct} does not exist`)
      return false
    }
    const ks = Array.from(us.keys())
    u = ks[0]
    console.log({ks,u})
  }
  console.log({u})
  if( ! d.has(ch) || ! d.get(ch).has(ct) || ! d.get(ch).get(ct).has(u) ) {
    return console.log(`Episode ${ch} ${ct} ${u} not in list`)
  }
  const v = d.get(ch).get(ct).get(u)
  console.log(`fetching ${{ch,ct,u,v}}`)
  cu = u
  dli = dlm.get(v)
  let epnum = `${ch}${ct}${cu}`
  if( epnum.length > 5 ) {
    epnum = epnum.substr(0,5)+"..."
  }
  q("div.nav2 .epnum").innerText = epnum
  setTimeout(upd_pushed,0)
  return fetch_episode(`${tl_name}/${ch}${ct}${cu}`)
}
const upd_pushed = _ => {
  let spans
  spans = qq("div.nav .hundreds .selector")
  spans.forEach(span => {
    const tx = span.innerText
    if( tx === ch ) {
      span.classList.add("selected")
    } else {
      span.classList.remove("selected")
    }
  })
  spans = qq("div.nav .tens .selector")
  spans.forEach(span => {
    const tx = span.innerText
    if( tx === ct ) {
      span.classList.add("selected")
    } else {
      span.classList.remove("selected")
    }
  })
  spans = qq("div.nav2 span.episodes .selector")
  console.log({spans})
  spans.forEach(span => {
    const tx = span.innerText
    if( tx === cu ) {
      span.classList.add("selected")
    } else {
      span.classList.remove("selected")
    }
  })
}
const sel = (h,t,u) => {
  console.log({h,t,u})
  sel_h(h) && sel_t(t) && sel_u(u)
}
const fetch_list = async _ => {
  try {
    list.forEach((line,i) => {
      const [ n, v ] = line
      const h = n[0], t = n[1], u = n.substr(2)
      console.log({a:"assign",h,t,u,v,line})
      d.assign(h,t,u,v)
      dl.push([h,t,u,v])
      dlm.set(v,i)
    })
    try {
      upd_hs()
    } catch(e) {
      console.log(`upd_hs fail ${e}`)
      throw(e)
    }
    try {
      sel("0","0","1")
    } catch(e) {
      console.log(`sel(0,0,1) fail ${e}`)
      throw(e)
    }
  } catch(e) {
    console.log(`Failed to get list == ${e}`)
  }
}
const fetch_episode = async ep => {
  const url = `tl_get.php?${ep}`
  try {
    const j = getUrl(url)
    console.log({j})
    const data = JSON.parse(j)
    console.log({data})
    if( data.error ) {
      return console.log(`Error ${data.error} getting ${url}`)
    }
    const { title, tracklist } = data
    q("div.tracklist .title").innerText = title
    console.log(11)
    q("div.tracklist .tracks").innerText = tracklist.trim()
    console.log(12)
  } catch(e) {
    console.log(`Failed to get episode ${ep} == ${e}`)
  }
}
const prev_u = _ => {
  dli = (dli + dl.length - 1) % dl.length
  const [ h, t, u, v ] = dl[dli]
  sel(h,t,u)
}
const next_u = _ => {
  dli = (dli + 1) % dl.length
  const [ h, t, u, v ] = dl[dli]
  sel(h,t,u)
}
const prev_t = _ => {
  const [ h, t, u, v ] = dl[dli]
  const ks = Array.from(d.get(h).keys())
  const i = ks.indexOf(t)
  const j = (i + ks.length - 1) % ks.length
  const tt = ks[j]
  sel_t(tt)
}
const next_t = _ => {
  const [ h, t, u, v ] = dl[dli]
  const ks = Array.from(d.get(h).keys())
  const i = ks.indexOf(t)
  const j = (i + 1) % ks.length
  const tt = ks[j]
  sel_t(tt)
}
const prev_h = _ => {
  const [ h, t, u, v ] = dl[dli]
  const ks = Array.from(d.keys())
  const i = ks.indexOf(h)
  const j = (i + ks.length - 1) % ks.length
  const hh = ks[j]
  sel_h(hh)
}
const next_h = _ => {
  const [ h, t, u, v ] = dl[dli]
  const ks = Array.from(d.keys())
  const i = ks.indexOf(h)
  const j = (i + 1) % ks.length
  const hh = ks[j]
  sel_h(hh)
}
const setup_keys = _ => {
  window.addEventListener("keydown", e => {
    const { key } = e
    switch(key.toLowerCase()) {
      case "arrowleft":
        if( e.ctrlKey ) return prev_h()
      case "a":
        e.preventDefault()
        return e.shiftKey ? prev_t() : prev_u()
      case "arrowright":
        if( e.ctrlKey ) return next_h()
      case "d":
        e.preventDefault()
        return e.shiftKey ? next_t() : next_u()
      case "s":
        e.preventDefault()
        return e.shiftKey ? prev_h() : prev_t()
      case "w":
        e.preventDefault()
        return e.shiftKey ? next_h() : next_t()
    }
  })
}
/*
      case "ArrowLeft":
        break;
      case "ArrowRight":
        dli = (dli + 1) % dl.length
        [ h, t, u, v ] = dl[dli]
        sel(h,t,u)
        break;
    }
}
*/
const init = async _ => {
  await fetch_list()
  const ep0 = list[0]
  const epn = ep0[0]
  await fetch_episode(epn) 
  setup_keys()
}
window.addEventListener("load",init)
</script>
<style>
div.tracklist {
  white-space: pre-wrap;
}
/*
div {
  border: 1px solid black;
  margin: 5px;
  padding: 5px;
}
*/
body {
  margin: 0px;
  padding: 0px;
  background: url(marble2.png), #000000 linear-gradient(to bottom, #ed4264 0%, #ffedbc 50%, #000000 100%) no-repeat;
  font-family: Optima, Helvetica, Arial;
}
div.nav , div.nav2 {
  background-color: #005;
  margin: 0px;
  padding-top: 1vh;
  padding-bottom: 1vh;
}
div.nav .hundreds {
  background-color: #008;
}
div.nav .tens {
  background-color: #00A;
}
div.nav2 .epnum {
  border: none;
  font-size: 1.5em;
  color: white;
}
div.nav2 .episodes {
  background-color: #00B;
}
div.nav , div.nav2 {
  text-align: center;
}
div.nav span , div.nav2 span {
  display: inline-block;
  border: 1px solid black;
  margin: 5px;
  padding: 5px;
}
span.selector {
  display: inline-block;
  border: 1px solid black;
  margin: 2px;
  padding: 2px;
  min-width: 1em;
  background-color: white;
  box-shadow: 3px 3px 3px black;
  cursor: pointer;
}
span.selected {
  color: white;
  font-weight: bold;
  background-color: black;
  box-shadow: 1px 1px 1px black;
  position: relative;
  left: 2px;
  top: 2px;
}
div.tracklist {
  margin: 2vh;
  border-radius: 2vh;
  background-color: white;  
  padding: 2vh;
  border: 1px solid black;
  box-shadow: 1vh 1vh 1vh black;
}
div.tracklist h2 {
}
div.tracklist .tracks {
  font-family: "Adobe Caslon Pro", sans-serif;
}
div.nav span.back {
  display: inline-block;
  background-color: #33c;
  box-shadow: .2em .2em .2em black;
}
div.nav span.back a {
  text-decoration: none;
  font-size: 1.2em;
  padding-left: 1em;
  padding-right: 1em;
  color: #ffa;
}
div.nav span.back a:visited {
  color: #ffa;
}
</style>
</head>
<body>
<div class="nav"><span class='back'><a href=".">Back</a></span><span class="hundreds"></span><span class="tens"></span></div>
<div class="nav2"><span class="epnum"></span><span class="episodes"></span></div>
<div class="tracklist"><h2 class='title'></h2><div class='tracks'></div></div>
</body>
</html>
