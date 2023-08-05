# Tool to export selected data from a musdb instance

The little script in this repository uses the [musdb API](https://demo.museum-digital.org/musdb/swagger/)
to download export data for specified search queries and copies to resulting
zip files to a folder set in the configuration.

## Requirements

- PHP 8
- Curl
- An API user for musdb with the user role "museum director" (all other permissions can be revoked)

## Background

At the German Digital Library (DDB) the FDH is listed as four separate organizations. museum-digital:musdb's quick export links for automating data exchanges on the other hand only export all (public) object data of an institution. For sending our data to the DDB in smaller batches (one per organizations), we thus need a more fine-grained, API-based solution which is offered in this repository.
