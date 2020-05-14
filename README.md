# Backpack-helpers package
Package created for additional functionality of **Backpack**.
https://backpackforlaravel.com/
## Image helper trait
Helps to store images on your disk
- Connect `ImageHelper` trait to you Model
```
class Post extends Model {
    ...
    use \BackpackHelpers\ImageHelper\ImageHelper
    ...
}
```
### Store image field:
- On setting attribute call trait method `handleStoreImageAttribute`
```php
class Model {
    use \BackpackHelpers\ImageHelper\ImageHelper;
    //...
    public function setImageAttribute($value) {
        $this->handleStoreImageAttribute('image', $value, 'uploads/posts', 'public');
    }
    //...
}
```
