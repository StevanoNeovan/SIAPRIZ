# Parser Refactoring Documentation

## Overview

Refactoring Parser system di SIAPRIZ untuk meningkatkan reusability, maintainability, dan extensibility. Sistem sekarang menggunakan **Strategy Pattern** dengan Column Mapper dan Status Mapper untuk menangani berbagai format marketplace.

## Architecture

### 1. Core Components

#### AbstractParser (Enhanced)
- **Lokasi**: `app/Services/Parser/AbstractParser.php`
- **Tanggung Jawab**:
  - Mengelola file reading dan parsing logic umum
  - Mengimplementasikan template method pattern untuk `parse()`
  - Menyediakan utility methods (cleanString, parseDecimal, parseInt, parseDate)
  - Mengelola transaction building

**Key Methods**:
- `parse()` - Generic parsing logic yang menggunakan column mapper
- `validate()` - Validasi format file berdasarkan required columns
- `parseOrder()` - Parse single order dengan multiple items
- `parseItems()` - Parse items dari order rows
- `parseFinancialData()` - Parse financial data (override di subclass)
- `parseCustomerData()` - Parse customer information
- `parseDateFromRow()` - Parse date dengan multiple date columns

#### ColumnMapperInterface
- **Lokasi**: `app/Services/Parser/Contracts/ColumnMapperInterface.php`
- **Tanggung Jawab**: Mendefinisikan contract untuk column mapping

**Methods**:
- `getRequiredColumns()` - Kolom yang harus ada di file
- `getColumnMapping()` - Mapping dari standard column ke file column
- `getOrderIdColumn()` - Kolom untuk grouping order
- `getDateColumns()` - Kolom untuk tanggal (bisa multiple dengan prioritas)
- `getStatusColumn()` - Kolom untuk status order

#### StatusMapperInterface
- **Lokasi**: `app/Services/Parser/Contracts/StatusMapperInterface.php`
- **Tanggung Jawab**: Mendefinisikan contract untuk status mapping

**Methods**:
- `mapStatus(string $status)` - Map status dari marketplace ke format standard

### 2. Marketplace-Specific Implementations

#### GenericParser
- **Lokasi**: `app/Services/Parser/GenericParser.php`
- **Gunakan untuk**: Template universal SIAPRIZ
- **Column Mapper**: `GenericColumnMapper`
- **Status Mapper**: `GenericStatusMapper`

#### ShopeeParser
- **Lokasi**: `app/Services/Parser/ShopeeParser.php`
- **Gunakan untuk**: Format CSV asli Shopee
- **Column Mapper**: `ShopeeColumnMapper`
- **Status Mapper**: `ShopeeStatusMapper`
- **Custom Logic**: 
  - `parseFinancialData()` - Menghitung diskon dari multiple fields
  - `parseItem()` - Custom SKU fallback logic

#### TokopediaParser
- **Lokasi**: `app/Services/Parser/TokopediaParser.php`
- **Gunakan untuk**: Format CSV asli Tokopedia
- **Column Mapper**: `TokopediaColumnMapper`
- **Status Mapper**: `TokopediaStatusMapper`
- **Custom Logic**:
  - `parseFinancialData()` - Menghitung komisi dan refund
  - `parseItem()` - Custom SKU fallback logic

#### LazadaParser
- **Lokasi**: `app/Services/Parser/LazadaParser.php`
- **Gunakan untuk**: Format CSV asli Lazada
- **Column Mapper**: `LazadaColumnMapper`
- **Status Mapper**: `LazadaStatusMapper`
- **Custom Logic**:
  - `parseFinancialData()` - Menghitung pendapatan bersih
  - `parseItem()` - Custom SKU fallback logic

### 3. Mapper Implementations

#### Column Mappers
Lokasi: `app/Services/Parser/Mappers/`

Setiap marketplace memiliki column mapper yang mendefinisikan:
- Required columns untuk validasi
- Mapping dari standard column names ke actual file column names
- Order ID column untuk grouping
- Date columns dengan prioritas
- Status column

**Contoh**:
```php
public function getColumnMapping(): array
{
    return [
        'order_id' => 'No. Pesanan',
        'status_order' => 'Status Pesanan',
        'sku' => 'SKU Induk',
        'nama_produk' => 'Nama Produk',
        // ... dst
    ];
}
```

#### Status Mappers
Lokasi: `app/Services/Parser/Mappers/`

Setiap marketplace memiliki status mapper yang mendefinisikan:
- Mapping dari status marketplace ke format standard
- Standard format: `selesai`, `proses`, `dibatalkan`, `dikembalikan`

## Usage

### Basic Usage

```php
use App\Services\Parser\GenericParser;
use App\Services\Parser\ShopeeParser;

// Untuk template SIAPRIZ
$parser = new GenericParser($file, $idPerusahaan, $idMarketplace);

// Untuk Shopee format asli
$parser = new ShopeeParser($file, $idPerusahaan, $idMarketplace);

// Validate
if (!$parser->validate()) {
    throw new Exception('Format tidak sesuai');
}

// Parse
$result = $parser->parse();
// Result: ['transactions' => [...], 'summary' => [...]]
```

### Integration dengan UploadService

```php
// UploadService.php sudah terintegrasi dengan parser baru
$parser = $this->getMarketplaceParser($file, $idPerusahaan, $idMarketplace);

if (!$parser->validate()) {
    throw new Exception('Format file tidak sesuai');
}

$parseResult = $parser->parse();
```

## Adding New Marketplace

Untuk menambah marketplace baru, ikuti langkah berikut:

### 1. Buat Column Mapper
```php
// app/Services/Parser/Mappers/NewMarketplaceColumnMapper.php
namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\ColumnMapperInterface;

class NewMarketplaceColumnMapper implements ColumnMapperInterface
{
    public function getRequiredColumns(): array
    {
        return ['Column1', 'Column2', ...];
    }
    
    public function getColumnMapping(): array
    {
        return [
            'order_id' => 'OrderID',
            'status_order' => 'Status',
            // ... dst
        ];
    }
    
    public function getOrderIdColumn(): string
    {
        return 'OrderID';
    }
    
    public function getDateColumns(): array
    {
        return ['OrderDate'];
    }
    
    public function getStatusColumn(): string
    {
        return 'Status';
    }
}
```

### 2. Buat Status Mapper
```php
// app/Services/Parser/Mappers/NewMarketplaceStatusMapper.php
namespace App\Services\Parser\Mappers;

use App\Services\Parser\Contracts\StatusMapperInterface;

class NewMarketplaceStatusMapper implements StatusMapperInterface
{
    public function mapStatus(string $status): string
    {
        $mapping = [
            'completed' => 'selesai',
            'pending' => 'proses',
            // ... dst
        ];
        
        return $mapping[strtolower($status)] ?? 'proses';
    }
}
```

### 3. Buat Parser Class
```php
// app/Services/Parser/NewMarketplaceParser.php
namespace App\Services\Parser;

use App\Services\Parser\Contracts\ColumnMapperInterface;
use App\Services\Parser\Contracts\StatusMapperInterface;
use App\Services\Parser\Mappers\NewMarketplaceColumnMapper;
use App\Services\Parser\Mappers\NewMarketplaceStatusMapper;

class NewMarketplaceParser extends AbstractParser
{
    protected function getColumnMapper(): ColumnMapperInterface
    {
        return new NewMarketplaceColumnMapper();
    }
    
    protected function getStatusMapper(): StatusMapperInterface
    {
        return new NewMarketplaceStatusMapper();
    }
    
    public function getMarketplaceCode(): string
    {
        return 'NEWMARKETPLACE';
    }
    
    // Override methods jika perlu custom logic
    protected function parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
    {
        // Custom logic untuk financial data
    }
}
```

### 4. Register di UploadService
```php
// app/Services/UploadService.php
private function getMarketplaceParser($file, int $idPerusahaan, int $idMarketplace)
{
    $marketplace = Marketplace::find($idMarketplace);
    
    $parsers = [
        'SHOPEE' => ShopeeParser::class,
        'TOKOPEDIA' => TokopediaParser::class,
        'LAZADA' => LazadaParser::class,
        'NEWMARKETPLACE' => NewMarketplaceParser::class, // Add here
    ];
    
    // ... rest of code
}
```

## Benefits

1. **Reusability**: AbstractParser menyediakan generic parsing logic yang dapat digunakan oleh semua marketplace
2. **Maintainability**: Column dan status mapping terpusat di mapper classes
3. **Extensibility**: Mudah menambah marketplace baru tanpa mengubah AbstractParser
4. **Testability**: Setiap mapper dapat ditest secara independen
5. **Separation of Concerns**: Parsing logic terpisah dari mapping logic
6. **DRY Principle**: Menghilangkan code duplication antar marketplace parsers

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

## Migration Notes

- Semua existing parsers sudah direfactor dan kompatibel dengan UploadService
- Tidak ada breaking changes untuk UploadService
- GenericParser sekarang fully functional dengan template SIAPRIZ
- Semua marketplace parsers menggunakan column mapper dan status mapper
