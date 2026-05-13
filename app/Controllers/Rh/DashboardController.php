namespace App\Controllers\Rh;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'rh') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        // Rediriger vers la page des demandes
        return redirect()->to(base_url('rh/demandes'));
    }
}