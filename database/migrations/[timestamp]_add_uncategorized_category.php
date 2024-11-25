use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class AddUncategorizedCategory extends Migration
{
    public function up()
    {
        // Create an uncategorized category if it doesn't exist
        if (!Category::where('slug', 'uncategorized')->exists()) {
            Category::create([
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'description' => 'Posts without a specific category'
            ]);
        }
    }

    public function down()
    {
        //
    }
} 