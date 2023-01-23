<?php
namespace App\Components\User\UserGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
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
	private Explorer $explorer;
    private UserRepository $userRepository;


    /**
	 * @param Explorer $explorer
	 */
	public function __construct(Explorer $explorer,
                                ITranslator $translator,
                                UserRepository $userRepository,
    )
	{
        parent::__construct($translator);
		$this->explorer = $explorer;
        $this->userRepository = $userRepository;
    }



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

		$grid->setDataSource($this->userRepository->findAll());

		$grid->addColumnText('id', 'app.userGrid.id');

        $grid->addColumnText('lastname', 'app.userGrid.lastname')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('firstname', 'app.userGrid.firstname')
            ->setSortable()
            ->setFilterText();


        $grid->addColumnText('email', 'app.userGrid.email')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('login', 'app.userGrid.login')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('workplace', 'app.userGrid.workplace')
            ->setSortable()
            ->setFilterText();

		$grid->addColumnText('user_role.type', 'app.userGrid.role')
            ->setRenderer(function( ActiveRow $row) {
            return $this->translator->translate($row->user_role->type);
        });

        $grid->addAction("edit", 'app.actions.edit', ":edit");

        $grid->addAction('delete','app.actions.delete')
            ->setConfirmation(
                new StringConfirmation($this->translator->translate('ublaboo_datagrid.delete_record_quote'))
            );;


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

