# antonrom00/laravel-model-changes-history

Records the changes history made to an eloquent model.

## Quick installation

```
composer require antonrom00/laravel-model-changes-history
```

```
php artisan vendor:publish --tag="model-history"
```

```
php artisan migrate
```

**Note: this library use `database` driver as default.**

## Installation
```
composer require antonrom00/laravel-model-changes-history
```

The package is auto discovered.

To change the config, publish it using the following command:
```
php artisan vendor:publish --provider="Antonrom\ModelChangesHistory\Providers\ModelChangesHistoryServiceProvider" --tag="config"
```

You can use three ways for record changes: `'default_driver' => 'database', 'file' or 'redis'`

If you want to use `database` driver, you must publish the migration file, run the following artisan commands:
```
php artisan vendor:publish --provider="Antonrom\ModelChangesHistory\Providers\ModelChangesHistoryServiceProvider" --tag="migrations"
```
```
php artisan migrate
```

Use this environment to manage library:
```
# Global recorgin model history changes
RECORD_CHANGES_HISTORY=true

# Default driver for recorgin model history changes
MODEL_CHANGES_HISTORY_DRIVER=database
```

## Usage

Add the trait to your model class you want to record changes for:
```php
use Antonrom\ModelChangesHistory\Traits\HasChangesHistory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model {
    use HasChangesHistory;
}

```

Your model now has a relation to all the changes made:
```php
$testModel->latestChange();

Antonrom\ModelChangesHistory\Models\Change {
    ...
    #attributes:  [
        "model_id" => 1
        "model_type" => "App\TestModel"
        "before_changes" => "{...}"
        "after_changes" => "{...}"
        "change_type" => "updated"
        "changes" => "{
            "title": {
                "before": "Some old title",  
                "after": "This is the new title"
            },
            "body": {
                "before": "Some old body",  
                "after": "This is the new body"
            }
        }"
        "changer_type" =>  "App\User"
        "changer_id" => 1
        "stack_trace" => "{...}"
        "created_at" => "2020-01-21 17:34:31"
    ]
    ...
}
```

Getting all changes history:
```php
$testModel->historyChanges();

Illuminate\Database\Eloquent\Collection {
  #items: array:3 [
    0 => Antonrom\ModelChangesHistory\Models\Change {...}
    1 => Antonrom\ModelChangesHistory\Models\Change {...}
    2 => Antonrom\ModelChangesHistory\Models\Change {...}
    ...
}
```

Clearing changes history:
```php
$testModel->clearHistoryChanges();
```

If you use `database` driver you can also use morph relations:
```php
$testModel->latestChangeMorph();
$testModel->historyChangesMorph();
```

#### Get an independent changes history:

```php
use Antonrom\ModelChangesHistory\Facades\HistoryStorage;
...

$latestChanges = HistoryStorage::getHistoryChanges(); // Return collection fo all latest changes
$latestChanges = HistoryStorage::getHistoryChanges($testModel); // Return collection fo all latest changes for model

$latestChange = HistoryStorage::getLatestChange(); // Return latest change
$latestChange = HistoryStorage::getLatestChange($testModel); // Return latest change for model

HistoryStorage::deleteHistoryChanges(); // This will delete all history changes
HistoryStorage::deleteHistoryChanges($testModel); // This will delete all history changes for model
```

Getting model changer:
```php
// Return Authenticatable `changer_type` using HasOne relation to changer_type and changer_id
$changer = $latestChange->changer; 
```

If you use `database` driver you can use Change model as:
```php
use Antonrom\ModelChangesHistory\Models\Change;

// Get the updates on the given model, by the given user, in the last 30 days:
Change::query()
    ->whereModel($testModel)
    ->whereChanger($user)
    ->whereType(Change::TYPE_UPDATED)
    ->whereCreatedBetween(now()->subDays(30), now())
    ->get();
```
