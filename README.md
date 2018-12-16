# GCS PHP Utility

Command line PHP utility for Google bucket API functions. Which can be used to upload and download files within a folder

## Getting Started

This package is available on packagist. You can use the following command to add this package into your composer dependancies.

``` composer require rizkhan/gce-php-utility --dev ```

### Configuration

You need to create a config.yml file in your project root. Below is the default config file.

```

---
bucket:
  name: your-bucket-name
  key: "keys/key-filename.json"
  syncfolder: "path/to/upload/and/download/dir/"
  uploadfiletypes: "jpg,gif,png,PNG,jpeg"

```

### Upload to GCS Bucket

You can execute the below command to upload all files and subfolders

``` vendor/bin/console gcs:upload ```

### Download from GCS Bucket

You can execute the below command to Download all files and subfolders

``` vendor/bin/console gcs:download ```

## Authors

* **Rizkhan Riaz** - 
[GitHub](https://github.com/rizkhanriaz)


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
