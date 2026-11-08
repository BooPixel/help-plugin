# How to Use SVN Credentials - Quick Summary

## Credentials Configured in Script

The `scripts/upload-to-svn.sh` script is already configured with:
- **Username:** `boopixel` (already in script, line 27)

## Where to Put the Password?

**You do NOT need to put the password in the script!**

The password will be requested **interactively** when you run SVN commands. This is more secure.

## Step by Step

### 1. Configure your SVN password on WordPress.org (if you haven't)

1. Visit: https://profiles.wordpress.org/me/profile/edit/group/3/?screen=svn-password
2. Generate your SVN password
3. Note the password

### 2. Execute the script

```bash
./scripts/upload-to-svn.sh
```

The script already uses the username `boopixel` automatically.

### 3. When prompted for password

When SVN asks for password, type:
- **Password for 'boopixel':** [your SVN password from WordPress.org]

### 4. Make commit

```bash
cd boopixel-svn
svn commit --username boopixel -m "Initial release version 1.0.0"
```

When prompted for password, type your SVN password again.

## Credentials Saved on macOS

After the first time, macOS will save your credentials in **Keychain**. Next times, you won't need to type the password again.

## Important

- **Username:** `boopixel` (already in script)
- **Password:** Type when prompted (don't put in script)
- **Never** put passwords in code files

## All Ready!

The script is already configured. You just need to:
1. Have SVN password configured on WordPress.org
2. Execute the script
3. Type password when prompted
