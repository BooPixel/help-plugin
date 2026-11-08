# How to Configure SVN Credentials

## Required Information

- **Username:** `boopixel` (case sensitive - use exactly like this)
- **Password:** The SVN password you configured in your WordPress.org profile

## Step 1: Configure SVN Password on WordPress.org

If you haven't configured your SVN password yet:

1. Visit: https://profiles.wordpress.org/me/profile/edit/group/3/?screen=svn-password
2. Follow the guide: https://make.wordpress.org/meta/handbook/tutorials-guides/svn-access/
3. Generate your SVN password and note it

## Options for Using Credentials

### Option 1: Enter Manually (Recommended for first time)

When you run SVN commands, it will ask:
- **Username:** Type `boopixel`
- **Password:** Type your SVN password

macOS will automatically save it to Keychain after the first time.

### Option 2: Specify in Command

```bash
svn commit --username boopixel -m "Initial release version 1.0.0"
```

### Option 3: Configure in Script

You can edit the `scripts/upload-to-svn.sh` script and add the username directly to SVN commands.

## Verify Saved Credentials

On macOS, credentials are saved in Keychain. You can check in:
- **Keychain Access app** → Search for "plugins.svn.wordpress.org"

## If You Need to Change Credentials

If you need to change saved credentials:

```bash
# Remove old credentials
svn auth --remove https://plugins.svn.wordpress.org

# Next time you use it, it will ask again
```

## Commands with Credentials

### Checkout
```bash
svn checkout --username boopixel https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n/ boopixel-svn
```

### Commit
```bash
svn commit --username boopixel -m "Initial release version 1.0.0"
```

### Update
```bash
svn update --username boopixel
```

## Important

- **NEVER** put your password directly in the script or code files
- Always use `--username` if you want to specify the user
- Password will be requested interactively (more secure)
- On macOS, password will be saved in Keychain after the first time
