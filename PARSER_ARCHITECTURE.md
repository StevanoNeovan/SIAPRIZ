# Parser Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         UploadService                                   │
│  - processUpload()                                                      │
│  - getMarketplaceParser()                                               │
│  - saveTransactions()                                                   │
└────────────────────────────────┬────────────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │                         │
                    ▼                         ▼
        ┌──────────────────────┐  ┌───────────────��──────┐
        │  useTemplate=true    │  │  useTemplate=false   │
        │  GenericParser       │  │  MarketplaceParser   │
        └──────────────────────┘  └──────────────────────┘
                    │                         │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │   AbstractParser        │
                    │  (Template Method)      │
                    │                         │
                    │  - parse()              │
                    │  - validate()           │
                    │  - parseOrder()         │
                    │  - parseItems()         │
                    │  - parseItem() [hook]   │
                    │  - parseFinancial...()  │
                    │  - parseCustomer...()   │
                    │  - Utilities            │
                    └────────────┬────────────┘
                                 │
                ┌───────────���────┼────────────────┐
                │                │                │
                ▼                ▼                ▼
        ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
        │ColumnMapper  │  │ StatusMapper  │  │ FileReader   │
        │              │  │              │  │              │
        │ - Required   │  │ - mapStatus()│  │ - readFile() │
        │   Columns    │  │              │  │ - getColumn  │
        │ - Mapping    │  │ Standard:    │  │   Value()    │
        │ - Order ID   │  │ - selesai    │  │              │
        │ - Date Cols  │  │ - proses     │  │              │
        │ - Status Col │  │ - dibatalkan │  │              │
        │              │  │ - dikembalikan│  │              │
        └──────────────┘  └──────────────┘  └──────────────┘
```

## Data Flow

```
CSV/Excel File
      │
      ▼
┌────────────────────────��────────────────┐
│  AbstractParser.readFile()              │
│  - Read using Excel facade              │
│  - Filter empty rows                    │
│  - Return Collection                    │
└─────────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────────┐
│  AbstractParser.validate()              │
│  - Get ColumnMapper                     │
│  - Check required columns               │
│  - Return bool                          │
└─────────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────────┐
│  AbstractParser.parse()                 │
│  - Get ColumnMapper & StatusMapper      │
│  - Group rows by Order ID               │
│  - For each order:                      │
│    - parseOrder()                       │
│      - parseItems()                     │
│      - parseFinancialData()             │
│      - parseDateFromRow()               │
│      - parseCustomerData()              │
│      - buildTransaction()               │
└─────────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────────┐
│  Result Array                           │
│  {                                      │
│    'transactions': [                    │
│      {                                  │
│        'header': {...},                 │
│        'items': [...]                   │
│      }                                  │
│    ],                                   │
│    'summary': {...}                     │
│  }                                      │
└─────────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────────┐
│  UploadService.saveTransactions()       │
│  - Save to database                     │
│  - Create audit logs                    │
│  - Return result                        │
└────���────────────────────────────────────┘
```

## Class Hierarchy

```
AbstractParser (abstract)
├── GenericParser
│   ├── GenericColumnMapper
│   └── GenericStatusMapper
├── ShopeeParser
│   ├── ShopeeColumnMapper
│   └── ShopeeStatusMapper
├── TokopediaParser
│   ├── TokopediaColumnMapper
│   └── TokopediaStatusMapper
└── LazadaParser
    ├── LazadaColumnMapper
    └── LazadaStatusMapper
```

## Mapper Pattern

```
┌─────────────────────────────────────────────────────────────┐
│  ColumnMapperInterface                                      │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ getRequiredColumns(): array                         │   │
│  │ getColumnMapping(): array                           │   │
│  │ getOrderIdColumn(): string                          │   │
│  │ getDateColumns(): array                             │   │
│  │ getStatusColumn(): string                           ��   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           ▲
                           │ implements
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ GenericCM    │  │ ShopeeCM     │  │ TokopediaCM  │
│              │  │              │  │              │
│ Required:    │  │ Required:    │  │ Required:    │
│ - No. Pesanan│  │ - No. Pesanan│  │ - Order ID   │
│ - Tanggal... │  │ - Status...  │  │ - Order...   │
│ - Status...  │  │ - Nama...    │  │ - Product... │
│ - SKU        │  │ - Jumlah     │  │ - Quantity   │
│ - Nama...    │  │ - Total...   │  │ - Order...   │
│ - Variasi    │  │              │  │              │
│ - Jumlah     │  │ Mapping:     │  │ Mapping:     │
│ - Harga...   │  │ order_id =>  │  │ order_id =>  │
│ - Total...   │  │ 'No. Pesanan'│  │ 'Order ID'   │
│ - Total...   │  │ status_order │  │ status_order │
│ - Ongkos...  │  │ => 'Status...'│  │ => 'Order...'│
│ - Biaya...   │  │              │  │              │
│ - Pendapatan │  │ OrderIdCol:  │  │ OrderIdCol:  │
│ - Nama...    │  │ 'No. Pesanan'│  │ 'Order ID'   │
│ - Kota       │  │              │  │              │
│ - Provinsi   │  │ DateCols:    │  │ DateCols:    │
│              │  │ - Waktu...   │  │ - Delivered..│
│ Mapping:     │  │ - Waktu...   │  │ - Cancelled..│
│ order_id =>  │  │              │  │ - Shipped... │
│ 'No. Pesanan'│  │ StatusCol:   │  │ - Paid...    │
│ status_order │  │ 'Status...   │  │ - Created... │
│ => 'Status...'│  │              │  │              │
│              │  │              │  │ StatusCol:   │
│ OrderIdCol:  │  │              │  │ 'Order Status│
│ 'No. Pesanan'│  │              │  │              │
│              │  │              │  │              │
│ DateCols:    │  ��              │  │              │
│ - Tanggal... │  │              │  │              │
│              │  │              │  │              │
│ StatusCol:   │  │              │  │              │
│ 'Status Order│  │              │  │              │
└──────────────┘  └──────────────┘  └──────────────┘
```

## Status Mapping

```
┌─────────────────────────────────────────────────────────────┐
│  StatusMapperInterface                                      │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ mapStatus(string $status): string                   │   │
│  │                                                     │   │
│  │ Standard Output:                                    │   │
│  │ - 'selesai'      (completed/delivered)             │   │
│  │ - 'proses'       (pending/processing)              │   │
│  │ - 'dibatalkan'   (cancelled)                       │   │
│  │ - 'dikembalikan' (returned/refunded)               │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           ▲
                           │ implements
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ GenericSM    │  │ ShopeeSM     │  │ TokopediaSM  │
│              │  │              │  │              │
│ Mapping:     │  │ Mapping:     │  │ Mapping:     │
│ completed => │  │ selesai =>   │  │ delivered => │
│ 'selesai'    │  │ 'selesai'    │  │ 'selesai'    │
│ delivered => │  │ completed => │  ��� finished =>  │
│ 'selesai'    │  │ 'selesai'    │  │ 'selesai'    │
│ selesai =>   │  │ delivered => │  │ completed => │
│ 'selesai'    │  │ 'selesai'    │  │ 'selesai'    │
│ success =>   │  │ sedang...=>  │  │ shipped =>   │
│ 'selesai'    │  │ 'proses'     │  │ 'proses'     │
│ pending =>   │  │ shipping =>  │  │ processed => │
│ 'proses'     │  │ 'proses'     │  │ 'proses'     │
│ processing =>│  │ siap...=>    │  │ on process =>│
│ 'proses'     │  │ 'proses'     │  │ 'proses'     │
│ proses =>    │  │ ready...=>   │  │ ready...=>   │
│ 'proses'     │  │ 'proses'     │  │ 'proses'     │
│ cancelled => │  │ dikemas =>   │  │ awaiting...=>│
│ 'dibatalkan' │  │ 'proses'     │  │ 'proses'     │
│ canceled =>  │  │ processing =>│  │ cancelled => │
│ 'dibatalkan' │  │ 'proses'     │  │ 'dibatalkan' │
│ dibatalkan =>│  │ dibatalkan =>│  │ canceled =>  │
│ 'dibatalkan' │  │ 'dibatalkan' │  │ 'dibatalkan' │
│ refunded =>  │  │ cancelled => │  │ returned =>  │
│ 'dikembalikan│  │ 'dibatalkan' │  │ 'dikembalikan│
│ returned =>  │  │ canceled =>  │  │ refunded =>  │
│ 'dikembalikan│  │ 'dibatalkan' │  │ 'dikembalikan│
│ dikembalikan │  │ batal =>     │  │              │
│ => 'dikembal │  │ 'dibatalkan' │  │              │
│ ikan'        │  │ pengembalian │  │              │
│              │  │ /penukaran =>│  │              │
│              │  │ 'dikembalikan│  │              │
│              │  │ returned =>  │  │              │
│              │  │ 'dikembalikan│  │              │
│              │  │ dikembalikan │  │              │
│              │  │ => 'dikembal │  │              │
│              │  │ ikan'        │  │              │
└──────────────┘  └──────────────┘  └──────────────┘
```

## Template Method Pattern

```
AbstractParser.parse()
│
├─ readFile()
│  └─ Excel::toCollection()
│
├─ validate()
│  ├─ getColumnMapper()
│  └─ Check required columns
│
├─ For each order:
│  │
│  ├─ parseOrder()
│  │  │
│  │  ├─ parseItems()
│  │  │  └─ parseItem() [HOOK - override in subclass]
│  │  │
│  │  ├─ parseFinancialData() [HOOK - override in subclass]
│  │  │
│  │  ├─ parseDateFromRow()
│  │  │
│  │  ├─ parseCustomerData() [HOOK - override in subclass]
│  │  │
│  │  └─ buildTransaction()
│  │
│  └─ Handle errors
│
└─ Return result array
```

## Extension Points (Hooks)

```
AbstractParser provides these extension points:

1. getColumnMapper(): ColumnMapperInterface
   └─ MUST override in subclass
   └─ Return mapper for marketplace

2. getStatusMapper(): StatusMapperInterface
   └─ MUST override in subclass
   └─ Return status mapper for marketplace

3. parseFinancialData(array $row, ColumnMapperInterface $columnMapper): array
   └─ CAN override in subclass
   └─ Default: uses column mapping
   └─ Override for custom financial calculation

4. parseItem(array $row, ColumnMapperInterface $columnMapper): ?array
   └─ CAN override in subclass
   └─ Default: uses column mapping
   └─ Override for custom item parsing

5. parseCustomerData(array $row, ColumnMapperInterface $columnMapper): array
   └─ CAN override in subclass
   └─ Default: uses column mapping
   └─ Override for custom customer data parsing
```

## Integration Points

```
UploadService
    │
    ├─ getMarketplaceParser()
    │  └─ Returns appropriate parser instance
    │
    ├─ parser.validate()
    │  └─ Validates file format
    │
    ├─ parser.parse()
    │  └─ Parses file and returns transactions
    │
    └─ saveTransactions()
       └─ Saves parsed data to database
```

## Adding New Marketplace - Step by Step

```
1. Create ColumnMapper
   └─ Implement ColumnMapperInterface
   └─ Define required columns
   └─ Define column mapping
   └─ Define order ID, date, status columns

2. Create StatusMapper
   └─ Implement StatusMapperInterface
   └─ Define status mapping to standard format

3. Create Parser
   └─ Extend AbstractParser
   └─ Implement getColumnMapper()
   └─ Implement getStatusMapper()
   └─ Implement getMarketplaceCode()
   └─ Override parseFinancialData() if needed
   └─ Override parseItem() if needed
   └─ Override parseCustomerData() if needed

4. Register in UploadService
   └─ Add to $parsers array in getMarketplaceParser()

5. Test
   └─ Unit test mapper
   └─ Unit test parser
   └─ Integration test with UploadService
```

## Performance Considerations

```
File Size    │ Processing Time │ Memory Usage │ Recommendation
─────────────┼─────────────────┼──────────────┼────────────────
< 1 MB       │ < 1 second      │ < 10 MB      │ Direct process
1-10 MB      │ 1-10 seconds    │ 10-50 MB     │ Direct process
10-50 MB     │ 10-60 seconds   │ 50-200 MB    │ Batch process
> 50 MB      │ > 60 seconds    │ > 200 MB     │ Queue job
```

## Error Handling

```
AbstractParser.parse()
│
├─ File read error
│  └─ Caught by readFile()
│  └─ Exception thrown
│
├─ Validation error
│  └─ validate() returns false
│  └─ UploadService throws exception
│
├─ Per-order error
│  └─ Caught in parseOrder() try-catch
│  └─ Added to errors array
│  └─ Processing continues
│
└─ Return result with errors array
   └─ UploadService logs errors
   └─ User notified of partial success
```

---

**Architecture Version**: 1.0
**Last Updated**: 2024
**Status**: Production Ready
