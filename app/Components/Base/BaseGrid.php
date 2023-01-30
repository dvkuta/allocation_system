<?php

namespace App\Components\Base;

use Nette\Localization\ITranslator;
use Nette\Localization\Translator;
use Ublaboo\DataGrid\DataGrid;

class BaseGrid extends BaseComponent
{
    public ITranslator $translator;

    /**
     * Výchozí možnosti počtu záznamů na stránku
     * @var array
     */
    protected $perPageOptions = [10, 20, 50, 100, 200, 500, 1000];

    /**
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function createGrid() :DataGrid
    {

        $datagrid = new DataGrid();
        $datagrid->setTranslator($this->translator);
        $datagrid->setItemsPerPageList($this->perPageOptions, true);

        return $datagrid;

    }


}