# Parser Quick Reference Guide

## File Locations

### Core Files
- `app/Services/Parser/AbstractParser.php` - Base class untuk semua parser
- `app/Services/Parser/GenericParser.php` - Parser untuk template SIAPRIZ
- `app/Services/Parser/ShopeeParser.php` - Parser untuk Shopee
- `app/Services/Parser/TokopediaParser.php` - Parser untuk Tokopedia
- `app/Services/Parser/LazadaParser.php` - Parser untuk Lazada

### Interfaces
- `app/Services/Parser/Contracts/ColumnMapperInterface.php`
- `app/Services/Parser/Contracts/StatusMapperInterface.php`

### Mappers
- `app/Services/Parser/Mappers/GenericColumnMapper.php`
- `app/Services/Parser/Mappers/GenericStatusMapper.php`
- `app/Services/Parser/Mappers/ShopeeColumnMapper.php`
- `app/Services/Parser/Mappers/ShopeeStatusMapper.php`
- `app/Services/Parser/Mappers/TokopediaColumnMapper.php`
- `app/Services/Parser/Mappers/TokopediaStatusMapper.php`
- `app/Services/Parser/Mappers/LazadaColumnMapper.php`
- `app/Services/Parser/Mappers/LazadaStatusMapper.php`

## How to Use

### Basic Usage
```php
use App\Services\Parser\GenericParser;

$parser = new GenericParser($file, $idPerusahaan, $idMarketplace);

// Validate
if (!$parser->validate()) {
    throw new Exception('Invalid format');
}

// Parse
$result = $parser->parse();
```

### Result Structure
```php
[
    'transactions' => [
        [
            'header' => [
                'id_perusahaan' => 1,
                'id_marketplace' => 1,
                'order_id' => 'ORD-001',
                'tanggal_order' => '2024-01-15',
                'status_order' => 'selesai',
                'total_pesanan' => 100000,
                'total_diskon' => 10000,
                'ongkos_kirim' => 5000,
                'biaya_komisi' => 2000,
                'pendapatan_bersih' => 93000,
                'nama_customer' => 'John Doe',
                'kota_customer' => 'Jakarta',
                'provinsi_customer' => 'DKI Jakarta',
            ],
            'items' => [
                [
                    'sku' => 'SKU-001',
                    'nama_produk' => 'Product Name',
                    'variasi' => 'Red, Size M',
                    'quantity' => 2,
                    'harga_satuan' => 50000,
                    'subtotal' => 100000,
                ],
            ],
        ],
    ],
    'summary' => [
        'total_orders' => 10,
        'total_rows' => 25,
        'errors' => [],
    ],
]
```

## Standard Status Values

Parser akan mengkonversi semua status ke salah satu dari:
- `selesai` - Order completed/delivered
- `proses` - Order in process/pending
- `dibatalkan` - Order cancelled
- `dikembalikan` - Order returned/refunded

## Adding New Marketplace

### 1. Create Column Mapper
```php
// app/Services/Parser/Mappers/NewMarketplaceColumnMapper.php
class NewMarketplaceColumnMapper implements ColumnMapperInterface
{
    public function getRequiredColumns(): array { ... }
    public function getColumnMapping(): array { ... }
    public function getOrderIdColumn(): string { ... }
    public function getDateColumns(): array { ... }
    public function getStatusColumn(): string { ... }
}
```

### 2. Create Status Mapper
```php
// app/Services/Parser/Mappers/NewMarketplaceStatusMapper.php
class NewMarketplaceStatusMapper implements StatusMapperInterface
{
    public function mapStatus(string $status): string { ... }
}
```

### 3. Create Parser
```php
// app/Services/Parser/NewMarketplaceParser.php
class NewMarketplaceParser extends AbstractParser
{
    protected function getColumnMapper(): ColumnMapperInterface { ... }
    protected function getStatusMapper(): StatusMapperInterface { ... }
    public function getMarketplaceCode(): string { ... }
}
```

### 4. Register in UploadService
```php
// app/Services/UploadService.php
$parsers = [
    'NEWMARKETPLACE' => NewMarketplaceParser::class,
];
```

## Customization

### Override parseFinancialData()
```php
protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
{
    // Custom logic untuk menghitung financial data
    return [
        'total_pesanan' => ...,
        'total_diskon' => ...,
        'ongkos_kirim' => ...,
        'biaya_komisi' => ...,
        'pendapatan_bersih' => ...,
    ];
}
```

### Override parseItem()
```php
protected function parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
{
    // Custom logic untuk parse item
    return [
        'sku' => ...,
        'nama_produk' => ...,
        'variasi' => ...,
        'quantity' => ...,
        'harga_satuan' => ...,
        'subtotal' => ...,
    ];
}
```

### Override parseCustomerData()
```php
protected function parseCustomerData(array $row, ColumnMapperInterface $columnMapper): array
{
    // Custom logic untuk parse customer data
    return [
        'nama_customer' => ...,
        'kota_customer' => ...,
        'provinsi_customer' => ...,
    ];
}
```

## Utility Methods

### cleanString($value)
Membersihkan dan normalize string
```php
$clean = $this->cleanString('  Hello World  ');
// Result: 'Hello World'
```

### parseDecimal($value)
Parse currency/decimal value
```php
$amount = $this->parseDecimal('Rp 100.000,50');
// Result: 100000.50
```

### parseInt($value)
Parse integer value
```php
$qty = $this->parseInt('5 pcs');
// Result: 5
```

### parseDate($value)
Parse date to Y-m-d format
```php
$date = $this->parseDate('15/01/2024');
// Result: '2024-01-15'
```

### getColumnValue($row, $columnName)
Get value dari row berdasarkan column name
```php
$value = $this->getColumnValue($row, 'Order ID');
```

## Troubleshooting

### Parser validation fails
- Check if all required columns exist in file
- Verify column names match exactly (case-sensitive)
- Check file format (CSV/Excel)

### Parse returns empty transactions
- Verify data rows exist after header
- Check if order ID column is correctly mapped
- Verify financial data columns exist

### Status not mapping correctly
- Check status mapper mapping
- Verify status value in file
- Add new status mapping if needed

### Custom financial calculation needed
- Override `parseFinancialData()` method
- Calculate based on available columns
- Return array with required keys

## Integration with UploadService

UploadService sudah terintegrasi dengan parser baru:

```php
// UploadService.php
public function processUpload($file, int $idPerusahaan, int $idPengguna, int $idMarketplace, bool $useTemplate = true): array
{
    if ($useTemplate) {
        $parser = new GenericParser($file, $idPerusahaan, $idMarketplace);
    } else {
        $parser = $this->getMarketplaceParser($file, $idPerusahaan, $idMarketplace);
        if (!$parser->validate()) {
            throw new Exception('Format tidak sesuai');
        }
    }
    
    $parseResult = $parser->parse();
    // ... process transactions
}
```

## Performance Tips

1. **Batch Processing**: Untuk file besar, process dalam batch
2. **Validation First**: Selalu validate sebelum parse
3. **Error Handling**: Catch exceptions per order, jangan stop semua
4. **Database Transactions**: Wrap saveTransactions dalam transaction

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Column not found | Verify column name di mapper |
| Status not recognized | Add mapping di status mapper |
| Financial calculation wrong | Override parseFinancialData() |
| SKU missing | Add fallback logic di parseItem() |
| Date parsing fails | Check date format, add to parseDate() |
| Memory error on large file | Process in chunks |

## Documentation Files

- `PARSER_REFACTORING.md` - Detailed documentation
- `PARSER_REFACTORING_SUMMARY.md` - Summary with examples
- `PARSER_QUICK_REFERENCE.md` - This file
