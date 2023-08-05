# Tool to export selected data from a musdb instance

The little script in this repository uses the [musdb API](https://demo.museum-digital.org/musdb/swagger/)
to download export data for specified search queries and copies to resulting
zip files to a folder set in the configuration.

## Requirements

- PHP 8
- Curl
- An API user for musdb with the user role "museum director" (all other permissions can be revoked)
