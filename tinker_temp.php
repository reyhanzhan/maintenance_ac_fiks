App\Models\RumahSakit::withCount('ruangans')->get(['id','nama'])->each(function($rs){ echo $rs->id.' | '.$rs->nama.' | ruangans: '.$rs->ruangans_count.PHP_EOL; });
