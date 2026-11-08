# FAQ - Frequently Asked Questions

Common questions and answers about the plugin upload and management process.

## Credentials and Authentication

### How do I configure my SVN password?

1. Visit: https://profiles.wordpress.org/me/profile/edit/group/3/?screen=svn-password
2. Follow the guide: https://make.wordpress.org/meta/handbook/tutorials-guides/svn-access/
3. Generate your SVN password and note it

### What is my SVN username?

Your SVN username is: **`boopixel`** (case sensitive - use exactly like this)

### Can I use my email as username?

No. SVN username is different from email. Always use `boopixel`.

### Where do I put the password in the script?

You **don't need** to put the password in the script. SVN will ask for the password interactively when needed. This is more secure.

## Upload and Commits

### How do I do the first upload?

1. Configure your SVN password
2. Run: `./scripts/upload-to-svn.sh`
3. Review changes: `cd boopixel-svn && svn status`
4. Commit: `svn commit --username boopixel -m "Initial release version 1.0.0"`

### How many commits can I make?

You can make as many commits as you want. SVN is a version control system, so each commit creates a new revision.

### Do I need to create a tag for each version?

Yes! Each version (1.0.0, 1.0.1, etc.) must have its own tag. This allows users to download specific versions.

## Structure and Files

### Where do screenshots go?

Screenshots go in `assets/screenshots/` at the SVN root, **not** in `trunk/assets/screenshots/`.

### Which files should NOT be sent?

- `.zip` files
- Build scripts (`build.sh`)
- `package.json`, `composer.json`
- Test files (`phpunit.xml`, `tests/` folder)
- `.gitignore`
- `README.md` (only README.txt)

### How do I update README.txt?

Edit the `README.txt` file and update the `Stable tag:` field with the new version before committing.

## Versions and Tags

### How do I create a new version?

1. Update version in main plugin file
2. Update `Stable tag:` in README.txt
3. Run upload script
4. Create a new tag: `svn copy trunk tags/1.0.1`
5. Commit

### Can I delete old tags?

No. Tags are historical and should not be deleted. Always create new tags for new versions.

## Viewing and Status

### How do I see which files will be committed?

```bash
cd boopixel-svn
svn status
```

### How do I see commit history?

```bash
cd boopixel-svn
svn log
```

### How do I see differences before committing?

```bash
svn diff
```

## Problems and Errors

### Error: "Authentication failed"

- Verify you're using the correct username: `boopixel`
- Verify you configured SVN password on WordPress.org
- Try removing old credentials: `svn auth --remove https://plugins.svn.wordpress.org`

### Error: "Path not found"

- Make sure you checked out the repository first
- Verify you're in the correct directory

### Error: "Working copy locked"

```bash
svn cleanup
```

### Plugin doesn't appear on WordPress.org after commit

It may take up to 72 hours to appear in search results. The plugin will be available immediately at the direct URL.

## Scripts

### Does the script commit automatically?

No. For security, the script only prepares files. You need to commit manually.

### Can I modify the scripts?

Yes! The scripts are yours and can be modified as needed.

### How do I update the version in the script?

Edit the `scripts/upload-to-svn.sh` file and change the `VERSION` variable on line 29.

## Resources

### Where can I find more help?

- [WordPress Plugin Developer FAQ](https://developer.wordpress.org/plugins/wordpress-org/plugin-developer-faq/)
- [Make WordPress Plugins](https://make.wordpress.org/plugins/)
- Email: plugins@wordpress.org

### Do I need to read all documentation?

No, but it's recommended to read at least:
- [Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [How to Use SVN](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)

---

**Didn't find your answer?** Check other guides in the documentation or contact plugins@wordpress.org
