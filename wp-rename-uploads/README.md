# wp-rename-uploads

Whenever there is a situation with wrongly named uploaded files - for example when they contain special characters like umlauts, this script could be used to fix database entries (`_wp_attached_file` and `_wp_attachment_metadata`) and filesystem (`uploads` folder).

Strongly recommended to use [Clean Image Filenames plugin](https://wordpress.org/plugins/clean-image-filenames/) to prevent further situations.
