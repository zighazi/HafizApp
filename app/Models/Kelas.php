namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model {
    protected $table = 'kelas';
    protected $fillable = ['grade','kode','nama_kelas','stream','is_special','next_kelas_id'];
    public function next()   { return $this->belongsTo(Kelas::class, 'next_kelas_id'); }
    public function santris(){ return $this->hasMany(Santri::class,'kelas_id'); }
}