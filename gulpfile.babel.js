import gulp from 'gulp';
import subprocess from 'child_process';
const P = Promise;
const log = (...args) => console.log(...args);

////////////////////////////////////////////////////////////////////////////////

const paths = {
  php: ['./src/**/*.php', './tests/**/*.php'],
};

const tasks = {};

tasks.phpunit = () => {
  let cmd = './vendor/phpunit/phpunit/phpunit';
  let args = ['--testsuite=unit'];
  let cp = subprocess.spawn(cmd, args, {stdio: 'inherit'})
  return new P((resolve, reject) => {
    cp.on('error', resolve);
    cp.on('close', resolve);
  });
};

tasks.watch = () => {
  gulp.watch(paths.php, ['phpunit']);
  return P.resolve();
};

gulp.task('phpunit', [], tasks.phpunit);
gulp.task('watch', ['phpunit'], tasks.watch);
gulp.task('default', ['watch']);
