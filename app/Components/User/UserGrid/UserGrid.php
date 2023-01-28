<?php
namespace App\Components\User\UserGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
use App\Model\User\Role\IRoleRepository;
use App\Model\User\Role\RoleRepository;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserRepository;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Row;

class UserGrid extends BaseGrid
{
    private UserRepository $userRepository;
    private IRoleRepository $roleRepository;


    /**
	 * @param Explorer $explorer
	 */
	public function __construct(
                                ITranslator $translator,
                                UserRepository $userRepository,
                                IRoleRepository $roleRepository
    )
	{
        parent::__construct($translator);
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();
		$grid->setDataSource($this->userRepository->findAll());

		$grid->addColumnText('id', 'app.user.id');

        $grid->addColumnLink('lastname', 'app.user.lastname', 'Project:user')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('firstname', 'app.user.firstname')
            ->setSortable()
            ->setFilterText();


        $grid->addColumnText('email', 'app.user.email')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('login', 'app.user.login')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('workplace', 'app.user.workplace')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('role_id', 'app.user.role', ':user_role.role_id')
        ->setRenderer(function (ActiveRow $row)
            {
                $roles = $row->related('user_role')
                    ->joinWhere($this->roleRepository->getTableName(),'role_id = role.id')
                    ->select('type')->fetchPairs('type','type');
                $roles = array_map(function ($role) {return $this->translator->translate($role);}, $roles);
                return implode(', ', $roles);
            });

        $grid->addAction("edit", 'app.actions.edit', ":edit");

        $grid->addAction('delete','app.actions.delete')
            ->setConfirmation(
                new StringConfirmation($this->translator->translate('ublaboo_datagrid.delete_record_quote'))
            );


		return $grid;
	}

    public function handleDelete(int $id)
    {
        $this->userRepository->delete($id);
        if($this->presenter->isAjax()) {
            /** @var BaseGrid $grid */
            $grid = $this["grid"];

            $grid->reload();
        }
        $this->presenter->flashMessage("Smazání proběhlo úspěšně", "bg-success");

    }


}


interface IUserGridFactory {

    public function create(): UserGrid;
}
