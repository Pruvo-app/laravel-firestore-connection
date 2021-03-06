<?php

namespace Pruvo\LaravelFirestoreConnection;

use App\Models\Course;
use Exception;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * @method static static|null first()
 * @method static static firstOrFail()
 * @method static static|\Illuminate\Database\Eloquent\Collection<static>|null find(string|string[]|\Illuminate\Contracts\Support\Arrayable $id)
 * @method static static findOrFail(string $id)
 * @method static static findOrNew(string $id)
 * @method static static findOrCreate(string $id, array $attributes = [], array $options = [])
 * @method static static|null firstWhere(string|\Google\Cloud\Firestore\FieldPath $path, string $operator = null, $value = null) This method has the same signature of `where()` method. Retrieves the first occurrence after applying the where condition.
 * @method static static firstOrNew(array $attributes = [], array $values = [])
 * @method static static firstOrCreate(array $attributes = [], array $values = [])
 * @method static static updateOrCreate(array $attributes = [], array $values = [])
 * @method static static firstOr(Closure $callback)
 * @method static static create(array $attributes = [], array $options = [])
 * @method static int count() Count the number of documents matching the query.
 * 
 * @method static \Illuminate\Database\Eloquent\Collection<static> findMany(string[]|\Illuminate\Contracts\Support\Arrayable $ids)
 * @method static \Illuminate\Database\Eloquent\Collection<static> get()
 * @method static \Illuminate\Database\Eloquent\Collection<static> all()
 * 
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate()
 * 
 * @method bool increment(string $attribute, int $amount = 1)
 * @method bool decrement(string $attribute, int $amount = 1)
 * 
 * @method static \Illuminate\Database\Eloquent\Builder inCollectionGroup()
 * @method static \Illuminate\Database\Eloquent\Builder select(string[]|\Google\Cloud\Firestore\FieldPath[] $paths)
 * @method static \Illuminate\Database\Eloquent\Builder whereIn(string|\Google\Cloud\Firestore\FieldPath $path, array $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereNotIn(string|\Google\Cloud\Firestore\FieldPath $path, array $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereIdIn(array $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereLessThan(string|\Google\Cloud\Firestore\FieldPath $path, $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereLessThanOrEqual(string|\Google\Cloud\Firestore\FieldPath $path, $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereGreaterThan(string|\Google\Cloud\Firestore\FieldPath $path, $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereGreaterThanOrEqual(string|\Google\Cloud\Firestore\FieldPath $path, $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereEqual(string|\Google\Cloud\Firestore\FieldPath $path, $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereNotEqual(string|\Google\Cloud\Firestore\FieldPath $path, $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereArrayContains(string|\Google\Cloud\Firestore\FieldPath $path, array $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereArrayContainsAny(string|\Google\Cloud\Firestore\FieldPath $path, array $values)
 * @method static \Illuminate\Database\Eloquent\Builder whereNull(string|\Google\Cloud\Firestore\FieldPath $path)
 * @method static \Illuminate\Database\Eloquent\Builder when(bool $condition, \Closure $ifCallback, \Closure $elseCallback = null)
 * @method static \Illuminate\Database\Eloquent\Builder latest(string|\Google\Cloud\Firestore\FieldPath $path = null)
 * @method static \Illuminate\Database\Eloquent\Builder offset(int $offset)
 * @method static \Illuminate\Database\Eloquent\Builder of(Model|DocumentReference|DocumentSnaphot $of)
 * @method static \Illuminate\Database\Eloquent\Builder orderBy(string|\Google\Cloud\Firestore\FieldPath $path, string $direction = 'ASC')
 * @method static \Illuminate\Database\Eloquent\Builder startAt(string[]|\Google\Cloud\Firestore\FieldPath[]|mixed[]|DocumentSnapshot)
 * @method static \Illuminate\Database\Eloquent\Builder startAfter(string[]|\Google\Cloud\Firestore\FieldPath[]|mixed[]|DocumentSnapshot)
 * @method static \Illuminate\Database\Eloquent\Builder endBefore(string[]|\Google\Cloud\Firestore\FieldPath[]|mixed[]|DocumentSnapshot)
 * @method static \Illuminate\Database\Eloquent\Builder endAt(string[]|\Google\Cloud\Firestore\FieldPath[]|mixed[]|DocumentSnapshot)
 * 
 * @method static \Illuminate\Database\Eloquent\Builder where(string|\Google\Cloud\Firestore\FieldPath $path, string $operator, $value)
 * #### Examples with operator
 * ```
 * where('name', '=', 'Mary')
 * // or
 * where('age', '>', 16)
 * ```
 * 
 * ***
 * 
 * #### Examples without operator
 * When the operator is ommited, the defaul is `=`
 * ```
 * where('name', 'Mary')
 * // is the same as 
 * where('name', '=', 'Mary')
 * ```
 * 
 * ***
 * 
 * #### Examples with group of wheres
 * ```
 * // Example 1
 * where([
 *      ['name', '=', 'Mary'],
 *      ['age', '>', 16],
 * ])
 * // is the same as 
 * where('name', '=', 'Mary')->where('age', '>', 16)
 * ```
 * Alternatively you can omit the operator as well.
 * ```
 * // Example 2
 * where([
 *      'name' => 'Mary',
 *      'age' => 16,
 * ])
 * // is the same as 
 * where('name', '=', 'Mary')->where('age', '=', 16)
 * ```
 * 
 * ***
 * 
 * #### List of operators
 * 
 * | Operator             	| Description           	| Alternative method          	|
 * |:----------------------	|:-----------------------	|:-----------------------------	|
 * | `=`                  	| Equal                 	| `whereEqual()`              	|
 * | `!=`                 	| Not Equal             	| `whereNotEqual()`           	|
 * | `in`                 	| In                    	| `whereIn()`                 	|
 * | `array-contains`     	| Array Contains        	| `whereArrayContains()`      	|
 * | `array-contains-any` 	| Array Contains Any    	| `whereArrayContainsAny()`   	|
 * | `>`                  	| Greater Than          	| `whereGreaterThan()`        	|
 * | `>=`                 	| Greater Than or Equal 	| `whereGreaterThanOrEqual()` 	|
 * | `<`                  	| Less Than             	| `whereLessThan()`           	|
 * | `<=`                 	| Less Than or Equal    	| `whereLessThanOrEqual()`    	|
 * 
 * ***
 */
trait Firebaseable
{
    /**
     * Additional metadata attributes managed by Scout.
     *
     * @var string
     */
    protected $documentReference;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootFirebaseable()
    {
        //
    }

    /**
     * Get the database connection for the model.
     *
     * @return \Pruvo\LaravelFirestoreConnection\FirestoreConnection
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Get Firestore client.
     *
     * @return \Google\Cloud\Firestore\FirestoreClient
     */
    public function getClient()
    {
        return $this->getConnection()->getClient();
    }

    /**
     * Get model instance from DocumentSnapshot.
     * 
     * @param \Google\Cloud\Firestore\DocumentSnapshot $documentSnapshot
     *
     * @return static
     */
    public static function fromDocumentSnapshot(DocumentSnapshot $documentSnapshot)
    {
        $model = new static();
        return $model->newFromBuilder($documentSnapshot, $model->getConnectionName());
    }

    /**
     * Get model instance from DocumentReference.
     * 
     * @param \Google\Cloud\Firestore\DocumentReference $documentReference
     *
     * @return static
     */
    public static function fromDocumentReference(DocumentReference $documentReference)
    {
        return static::fromDocumentSnapshot($documentReference->snapshot());
    }

    public function subCollection($collection, $model)
    {
        $model = new $model;
        $table = $this
            ->getDocumentReference()
            ->collection($collection ?: $model->getTable())
            ->path();

        return $model->setTable($table)->setParentModel($this);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        $collection = parent::newCollection($models);

        if (is_object($parentModel = $this->getParentModel())) {
            $collection
                ->transform(function ($model) use ($parentModel) {
                    return $model->setParentModel($parentModel);
                });
        }

        return $collection;
    }

    /**
     * Get metadata
     *
     * @return array
     * @deprecated
     */
    public function getMetadata()
    {
        return [
            'id' => $this->getKey(),
            'createTime' => optional($this->created_at)->format(Carbon::W3C),
            'updateTime' => optional($this->updated_at)->format(Carbon::W3C),
        ];
    }

    /**
     * Set parent model.
     * 
     * @param string|\Illuminate\Database\Eloquent\Model $model
     * @return static
     * @throws \InvalidArgumentException
     */
    public function setParentModel($model)
    {
        if (
            $model instanceof Model
            || (is_string($model) && class_exists($model) && is_subclass_of($model, Model::class))
        ) {
            $this->parentModel = $model;
            return $this;
        }

        throw new InvalidArgumentException(
            sprintf("Parent model must be an instance of %s or a string representing a class name.", Model::class)
        );
    }

    /**
     * Parent Model
     * Return a model instance with parent collection document
     *
     * @return Model|null
     */
    public function getParentModel(): ?Model
    {
        // if this model has a parent model instance, then return the instance
        if ($this->parentModel instanceof Model) {
            return $this->parentModel;
        } 
        
        // if this model has a prent model class, then return a new instance of it
        elseif (
            is_string($this->parentModel)
            && class_exists($this->parentModel)
            && new $this->parentModel() instanceof self
        ) {
            $docRef = $this->getDocumentReference();
            if (!$docRef instanceof DocumentReference) {
                throw new Exception(sprintf("DocumentReference not found on %s.", static::class));
            }

            $parentDocumentReference = $this->getClient()->document(
                $docRef->parent()->parent()->path()
            );

            if ($parentDocumentReference) {
                return $this->parentModel = $this->parentModel::fromDocumentReference($parentDocumentReference);
            }
            else {
                throw new Exception(sprintf("Parent document not found on %s.", static::class));
            }
        }
        
        // sometimes models does not have a parent model
        return null;
    }

    /**
     * Get model document reference
     *
     * @return DocumentReference
     */
    public function getDocumentReference()
    {
        return $this->getConnection()->getClient()->document($this->documentReference);
    }

    /**
     * Set DocumentReference on model
     *
     * @return static
     */
    public function setDocumentReference($documentReference)
    {
        $this->documentReference = $documentReference instanceof DocumentReference
            || $documentReference instanceof DocumentSnapshot
            ? $documentReference->path()
            : $documentReference;

        return $this;
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifyColumn($column)
    {
        return $column;
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array|\Google\Cloud\Firestore\DocumentSnapshot  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if ($attributes instanceof DocumentSnapshot) {
            return parent::newFromBuilder($attributes->data(), $connection)
                ->setDocumentReference($attributes->reference())
                ->setKeyType('string')
                ->setIncrementing(false);
        }

        return parent::newFromBuilder($attributes, $connection);
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);

        if (is_object($parentModel = $this->getParentModel())) {
            $model->setParentModel($parentModel);
        }

        return $model;
    }

    /**
     * Delete document and subcollections in document recursivelly
     *
     * @return string
     */
    protected function performDelete(DocumentReference $documentReference, int $batchSize = 1000): void
    {
        foreach ($documentReference->collections() as $subcollection) {
            $documents = $subcollection->limit($batchSize)->select([])->documents();
            while (!$documents->isEmpty()) {
                foreach ($documents as $doc) {
                    $this->performDelete($doc->reference(), $batchSize);
                }
                $documents = $subcollection->limit($batchSize)->documents();
            }
        }
        $documentReference->delete();
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        $this->performDelete($this->getDocumentReference());
        parent::performDeleteOnModel();
    }

    /**
     * Insert the given attributes and set the ID on the model.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $attributes
     * @return void
     */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        /** @var \Google\Cloud\Firestore\DocumentReference */
        $documentReference = $query->insertGetId($attributes, $keyName = $this->getKeyName());

        $this
            ->setAttribute($keyName, $documentReference->id())
            ->setDocumentReference($documentReference)
            ->setKeyType('string');
    }

    /**
     * Add the casted attributes to the attributes array.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function castAttributesToFirebase(array $attributes)
    {
        foreach ($this->getCasts() as $key => $value) {
            if (!array_key_exists($key, $attributes)) {
                continue;
            }

            // Here we will cast the attribute. Then, if the cast is a date or datetime cast
            // then we will serialize the date for the array. This will convert the dates
            // to strings based on the date format specified for these Eloquent models.
            $attributes[$key] = $this->castAttribute(
                $key,
                $attributes[$key]
            );

            // If the attribute cast was a date or a datetime, we will serialize the date as
            // a string. This allows the developers to customize how dates are serialized
            // into an array without affecting how they are persisted into the storage.

            if ($attributes[$key] && $this->isClassSerializableToFirestore($key)) {
                $attributes[$key] = $this->serializeToFirestoreClassCastableAttribute($key, $attributes[$key]);
            }

            if ($attributes[$key] instanceof Arrayable) {
                $attributes[$key] = $attributes[$key]->toArray();
            }
        }

        return $attributes;
    }

    /**
     * Determine if the key is serializable using a custom class.
     *
     * @param  string  $key
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\InvalidCastException
     */
    protected function isClassSerializableToFirestore($key)
    {
        return $this->isClassCastable($key) &&
            method_exists($this->parseCasterClass($this->getCasts()[$key]), 'toFirestore');
    }

    /**
     * Serialize the given attribute using the custom cast class.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function serializeToFirestoreClassCastableAttribute($key, $value)
    {
        return $this->resolveCasterClass($key)->toFirestore(
            $this,
            $key,
            $value,
            $this->attributes
        );
    }

    /**
     * Get all of the current attributes on the model for an insert operation.
     *
     * @return array
     */
    protected function getAttributesForInsert()
    {
        return $this->castAttributesToFirebase(
            parent::getAttributesForInsert()
        );
    }

    /**
     * Get the attributes that have been changed since the last sync.
     *
     * @return array
     */
    public function getDirty()
    {
        return $this->castAttributesToFirebase(
            parent::getDirty()
        );
    }
}
