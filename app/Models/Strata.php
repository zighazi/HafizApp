namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Strata extends Model {
    protected $table = 'strata';
    protected $fillable = ['stream','nama_strata'];
}