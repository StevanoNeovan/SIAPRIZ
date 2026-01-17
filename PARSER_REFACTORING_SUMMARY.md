# SIAPRIZ Parser Refactoring - Summary

## What Was Done

Refactored the Parser system di SIAPRIZ untuk meningkatkan reusability dan maintainability. Sistem sekarang menggunakan **Strategy Pattern** dengan Column Mapper dan Status Mapper.

## Key Changes

### 1. Enhanced AbstractParser
- Sekarang mengimplementasikan generic `parse()` method yang dapat digunakan oleh semua marketplace
- Menghilangkan code duplication dengan menyediakan template method pattern
- Menambahkan support untuk column mapping dan status mapping
- Menyediakan hook methods untuk custom logic di subclass:
  - `parseFinancialData()` - Override untuk custom financial calculation
  - `parseItem()` - Override untuk custom item parsing
  - `parseCustomerData()` - Override untuk custom customer data parsing

### 2. New Interfaces
- **ColumnMapperInterface**: Mendefinisikan contract untuk column mapping
- **StatusMapperInterface**: Mendefinisikan contract untuk status mapping

### 3. Mapper Classes
Setiap marketplace sekarang memiliki 2 mapper classes:

**Column Mappers** (di `app/Services/Parser/Mappers/`):
- `GenericColumnMapper` - Untuk template SIAPRIZ
- `ShopeeColumnMapper` - Untuk Shopee format asli
- `TokopediaColumnMapper` - Untuk Tokopedia format asli
- `LazadaColumnMapper` - Untuk Lazada format asli

**Status Mappers** (di `app/Services/Parser/Mappers/`):
- `GenericStatusMapper` - Untuk template SIAPRIZ
- `ShopeeStatusMapper` - Untuk Shopee
- `TokopediaStatusMapper` - Untuk Tokopedia
- `LazadaStatusMapper` - Untuk Lazada

### 4. Refactored Parsers
- **GenericParser** - Sekarang fully functional dengan template SIAPRIZ
- **ShopeeParser** - Simplified, hanya override custom logic
- **TokopediaParser** - Simplified, hanya override custom logic
- **LazadaParser** - Simplified, hanya override custom logic

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    AbstractParser                           │
│  - parse() [Template Method]                                │
│  - validate()                                               │
│  - parseOrder()                                             │
│  - parseItems()                                             │
│  - parseFinancialData() [Hook - override di subclass]       │
│  - parseItem() [Hook - override di subclass]                │
│  - parseCustomerData() [Hook - override di subclass]        │
│  - Utility methods (cleanString, parseDecimal, etc)         │
└─────────────────────────────────────────────────────────────┘
         ▲                    ▲                    ▲
         │                    │                    │
    ┌────┴────┐          ┌────┴────┐          ┌────┴────┐
    │ Generic │          │ Shopee  │          │Tokopedia│
    │ Parser  │          │ Parser  │          │ Parser  │
    └────┬────┘          └────┬────┘          └────┬────┘
         │                    │                    │
    ┌────▼────────────────────▼────────────────────▼────┐
    │         Uses ColumnMapper & StatusMapper          │
    │                                                    │
    │  ┌──────────────────────────────────────────┐    │
    │  │  ColumnMapperInterface                   │    │
    │  │  - getRequiredColumns()                  │    │
    │  │  - getColumnMapping()                    │    │
    │  │  - getOrderIdColumn()                    │    │
    │  │  - getDateColumns()                      │    │
    │  │  - getStatusColumn()                     │    │
    │  └──────────────────────────────────────────┘    │
    │                                                    │
    │  ┌──────────────────────────────────────────┐    │
    │  │  StatusMapperInterface                   │    │
    │  │  - mapStatus(string $status)             │    │
    │  └──────────────────────────────────────────┘    │
    └──────────────────────────────────���─────────────────┘
```

## Usage Examples

### Example 1: Parse Shopee File
```php
use App\Services\Parser\ShopeeParser;

$file = request()->file('csv');
$parser = new ShopeeParser($file, $idPerusahaan, $idMarketplace);

// Validate format
if (!$parser->validate()) {
    return response()->json(['error' => 'Format tidak sesuai Shopee'], 422);
}

// Parse file
$result = $parser->parse();

// Result structure:
// [
//     'transactions' => [
//         [
//             'header' => [...],
//             'items' => [...]
//         ],
//         ...
//     ],
//     'summary' => [
//         'total_orders' => 10,
//         'total_rows' => 25,
//         'errors' => []
//     ]
// ]
```

### Example 2: Parse Generic Template
```php
use App\Services\Parser\GenericParser;

$file = request()->file('csv');
$parser = new GenericParser($file, $idPerusahaan, $idMarketplace);

if (!$parser->validate()) {
    return response()->json(['error' => 'Format template SIAPRIZ tidak sesuai'], 422);
}

$result = $parser->parse();
```

### Example 3: Add New Marketplace (Bukalapak)

**Step 1: Create Column Mapper**
```php
// app/Services/Parser/Mappers/BukalapakColumnMapper.php
namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\ColumnMapperInterface;

class BukalapakColumnMapper implements ColumnMapperInterface
{
    public function getRequiredColumns(): array
    {
        return ['Order ID', 'Status', 'Product', 'Qty', 'Total'];
    }
    
    public function getColumnMapping(): array
    {
        return [
            'order_id' => 'Order ID',
            'status_order' => 'Status',
            'sku' => 'SKU',
            'nama_produk' => 'Product',
            'quantity' => 'Qty',
            'total_pesanan' => 'Total',
            // ... dst
        ];
    }
    
    public function getOrderIdColumn(): string
    {
        return 'Order ID';
    }
    
    public function getDateColumns(): array
    {
        return ['Order Date'];
    }
    
    public function getStatusColumn(): string
    {
        return 'Status';
    }
}
```

**Step 2: Create Status Mapper**
```php
// app/Services/Parser/Mappers/BukalapakStatusMapper.php
namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\StatusMapperInterface;

class BukalapakStatusMapper implements StatusMapperInterface
{
    public function mapStatus(string $status): string
    {
        $mapping = [
            'selesai' => 'selesai',
            'pending' => 'proses',
            'batal' => 'dibatalkan',
            'return' => 'dikembalikan',
        ];
        
        return $mapping[strtolower($status)] ?? 'proses';
    }
}
```

**Step 3: Create Parser**
```php
// app/Services/Parser/BukalapakParser.php
namespace App\Services\Parser;

use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\BukalapakColumnMapper;
use App\Services\Parser\Mappers\BukalapakStatusMapper;

class BukalapakParser extends AbstractParser
{
    protected function getColumnMapper(): ColumnMapperInterface
    {
        return new BukalapakColumnMapper();
    }
    
    protected function getStatusMapper(): StatusMapperInterface
    {
        return new BukalapakStatusMapper();
    }
    
    public function getMarketplaceCode(): string
    {
        return 'BUKALAPAK';
    }
    
    // Override jika perlu custom logic
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        // Custom logic untuk Bukalapak
        return parent::parseFinancialData($row, $columnMapper);
    }
}
```

**Step 4: Register di UploadService**
```php
// app/Services/UploadService.php
private function getMarketplaceParser($file, int $idPerusahaan, int $idMarketplace)
{
    $marketplace = Marketplace::find($idMarketplace);
    
    $parsers = [
        'SHOPEE' => ShopeeParser::class,
        'TOKOPEDIA' => TokopediaParser::class,
        'LAZADA' => LazadaParser::class,
        'BUKALAPAK' => BukalapakParser::class, // Add here
    ];
    
    $parserClass = $parsers[$marketplace->kode_marketplace] ?? null;
    
    if (!$parserClass) {
        throw new \Exception('Parser untuk marketplace ini belum tersedia.');
    }
    
    return new $parserClass($file, $idPerusahaan, $idMarketplace);
}
```

## Benefits

1. **Reusability**: AbstractParser menyediakan generic parsing logic
2. **Maintainability**: Column dan status mapping terpusat
3. **Extensibility**: Mudah menambah marketplace baru
4. **Testability**: Setiap mapper dapat ditest independen
5. **Separation of Concerns**: Parsing logic terpisah dari mapping logic
6. **DRY Principle**: Menghilangkan code duplication

## File Structure

```
app/Services/Parser/
├── Contracts/
│   ├── ColumnMapperInterface.php
│   └── StatusMapperInterface.php
├── Mappers/
│   ├── GenericColumnMapper.php
│   ├── GenericStatusMapper.php
│   ├── ShopeeColumnMapper.php
│   ├── ShopeeStatusMapper.php
│   ├── TokopediaColumnMapper.php
│   ├── TokopediaStatusMapper.php
│   ├── LazadaColumnMapper.php
│   └── LazadaStatusMapper.php
├── AbstractParser.php
├── GenericParser.php
├── ShopeeParser.php
├── TokopediaParser.php
└── LazadaParser.php
```

## Testing

### Test GenericParser
```php
public function test_generic_parser_validates_template()
{
    $file = UploadedFile::fake()->create('sales.csv');
    $parser = new GenericParser($file, 1, 1);
    
    $this->assertTrue($parser->validate());
}

public function test_generic_parser_parses_transactions()
{
    $file = UploadedFile::fake()->create('sales.csv');
    $parser = new GenericParser($file, 1, 1);
    
    $result = $parser->parse();
    
    $this->assertArrayHasKey('transactions', $result);
    $this->assertArrayHasKey('summary', $result);
}
```

### Test ShopeeParser
```php
public function test_shopee_parser_validates_format()
{
    $file = UploadedFile::fake()->create('shopee.csv');
    $parser = new ShopeeParser($file, 1, 1);
    
    $this->assertTrue($parser->validate());
}
```

## Migration Checklist

- [x] Create ColumnMapperInterface
- [x] Create StatusMapperInterface
- [x] Enhance AbstractParser dengan generic parse() method
- [x] Create GenericColumnMapper & GenericStatusMapper
- [x] Create ShopeeColumnMapper & ShopeeStatusMapper
- [x] Create TokopediaColumnMapper & TokopediaStatusMapper
- [x] Create LazadaColumnMapper & LazadaStatusMapper
- [x] Refactor GenericParser
- [x] Refactor ShopeeParser
- [x] Refactor TokopediaParser
- [x] Refactor LazadaParser
- [x] Verify UploadService compatibility
- [x] Create documentation

## Next Steps

1. Test semua parser dengan sample files
2. Update unit tests untuk new architecture
3. Add integration tests untuk UploadService
4. Monitor production untuk edge cases
5. Dokumentasi untuk developers tentang cara menambah marketplace baru
