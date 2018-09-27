<?php
namespace App\Components;

use NumberFormatter;
use Money\Currencies\ISOCurrencies;
use Money\Money as MoneyLibrary;
use Money\Parser\DecimalMoneyParser;
use Money\Formatter\IntlMoneyFormatter;

use App\Vendor\Money\Formatter\DecimalMoneyFormatter;

class Money
{
    
    protected static $instance = null;
    protected $parser = null;
    protected $formatter = null;
    protected $is_taxable = false;
    
    public static function i()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
    
    public function __construct()
    {
        $this->parser = new DecimalMoneyParser(
            new ISOCurrencies()
        );
        
        $this->dollarFormatter = new IntlMoneyFormatter(
            new NumberFormatter('en_US', NumberFormatter::CURRENCY),
            new ISOCurrencies()
        );
        
        $this->numberFormatter = new DecimalMoneyFormatter(
            new NumberFormatter('en_US', NumberFormatter::CURRENCY),
            new ISOCurrencies()
        );
    }
    
    public function parse($amount)
    {
        if ($amount === null) {
            $amount = 0.0;
        }
        
        return $this->parser->parse((string)$amount, 'USD');
    }
    
    public function format($money)
    {
        return $this->dollarFormatter->format($money);
    }
    
    public function amount($money)
    {
        return $this->numberFormatter->format($money);
    }
    
    public static function USD($amount)
    {
        return static::i()->parse($amount);
    }
    
    public function setTaxable($taxable)
    {
        $this->is_taxable = $taxable;
    }
    
    public function isTaxable()
    {
        return (
            auth()->user()
            && auth()->user()->id
            && auth()->user()->is_taxable
        )
        || $this->is_taxable;
    }
    
    public static function applyTax($value)
    {
        if (static::i()->isTaxable()) {
            $price = static::USD($value);
            return static::i()->amount(
                $price->add(
                    static::getTaxMoney($value)
                )
            );
        }
        else {
            return $value;
        }
    }
    
    public static function getTaxMoney($value)
    {
        if (static::i()->isTaxable()) {
            $taxPercent = config('settings.money.store_owner_tax_percent');
            
            $price = static::USD($value);
            return $price->multiply($taxPercent/100);
        }
        else {
            return static::USD(0);
        }
    }
    
    public static function getTax($value)
    {
        return static::i()->amount(
            static::getTaxMoney($value)
        );
    }
}
