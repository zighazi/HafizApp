namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model {
    protected $table = 'angkatans';
    protected $fillable = ['tahun','label'];
}