
require_once 'Zend/Controller/Action.php';

/**
 * Base Home Controller
 * 
 * @category   app
 * @package    app_controller
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class HomeController extends Zend_Controller_Action
{

	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		$homePage = new <?php print $TOOLNAME;?>_app_HomePage();
		$homePage->setBodyAttributes(array('class'=>'weAppHome'));
		echo $homePage->getHTML();
	}
	
}
