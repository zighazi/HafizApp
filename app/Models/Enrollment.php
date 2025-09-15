namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model {
    protected $fillable = ['santri_id','tahun_ajaran','angkatan_id','kelas_id','strata_id','promoted_at'];
    public function santri(){ return $this->belongsTo(Santri::class); }
}