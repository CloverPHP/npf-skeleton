<?php

namespace Model;

use Npf\Core\App;
use \Npf\Core\Model;
use Npf\Exception\InternalError;

/**
 * Class Loader
 * @property Admin $Admin
 * @property AdminManager $AdminManager
 * @property AdminMenu $AdminMenu
 * @property AdminRole $AdminRole
 * @property AdminLogin $AdminLogin
 * @property BillingInvoice $BillingInvoice
 * @property BillingInvoiceItem $BillingInvoiceItem
 * @property BillingQuotation $BillingQuotation
 * @property BillingQuotationItem $BillingQuotationItem
 * @property BillingPO $BillingPO
 * @property BillingPOItem $BillingPOItem
 * @property GalleryPath $GalleryPath
 * @property GalleryPhoto $GalleryPhoto
 * @property ProfileCompany $ProfileCompany
 * @property ProfileCustomer $ProfileCustomer
 * @property ProfileVendor $ProfileVendor
 * @property AccMainLedger $AccMainLedger
 * @property AccSubLedger $AccSubLedger
 * @property AccTrx $AccTrx
 * @property AccPostedMonth $AccPostedMonth
 * @property AccSummary $AccSummary
 * @property Setting $Setting
 * @property StockItem $StockItem
 * @property StockIn $StockIn
 * @property StockOut $StockOut
 * @property StockStatistics $StockStatistics
 * @property OAuthConnect $OAuthConnect
 * @property VehicleCar $VehicleCar
 * @property VehiclePetrol $VehiclePetrol
 */
final class Loader extends Model
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var array
     */
    private $components = [];


    /**
     * Loader constructor.
     * @param App $app
     */
    final public function __construct(App &$app)
    {
        parent::__construct($app);
    }

    /**
     * @param $name
     * @return mixed
     * @throws InternalError
     */
    final public function __get($name)
    {
        if (!isset($this->components[$name]))
            $this->components[$name] = $this->app->model($name);
        return $this->components[$name];
    }
}