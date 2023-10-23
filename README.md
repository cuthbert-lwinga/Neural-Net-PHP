# Neural-Net-PHP 🧠💻

![Build Status](https://img.shields.io/badge/build-passing-brightgreen) ![PHP Version](https://img.shields.io/badge/php-^7.4-blue) ![Unit Tests](https://img.shields.io/badge/tests-100%25-green)

## Table of Contents 📚
- [Introduction](#introduction-🎉)
- [Installation](#installation-🛠)
- [Usage](#usage-🚀)
- [Testing](#testing-🔬)
- [Features](#features-✨)
- [Technical Considerations](#technical-considerations-🤔)
- [Acknowledgements](#acknowledgements-🙏)
- [License](#license-📝)

## Introduction 🎉
Welcome to Neural-Net-PHP. This library provides a framework for implementing neural networks in PHP. It's an adaptation inspired by the book "Neural Networks from Scratch in Python" by Harrison Kinsley.

## Installation 🛠

To set up the library, clone the repository and initialize it with Composer.

```bash
git clone https://github.com/cuthbert-lwinga/Neural-Net-PHP.git
cd Neural-Net-PHP
composer install
```

## Usage 🚀
To utilize the library, simply import the relevant modules. Comprehensive documentation and usage guidelines will be available soon.

```php
// Some uber-cool example code goes here!
```

## Testing 🔬

To set up the library, clone the repository and initialize it with Composer.

```bash
./vendor/bin/phpunit
```

You should see a looong list of passed tests like:

```
TESTING....[testZeros1D]
.
...
TESTING....[Activation_Softmax_Loss_CategoricalCrossentropy Forward]
.
```

All tests should pass, and we're talking zero room for error—tolerance of just \(0.00001\)! 🎯

## Features ✨

- 🧠 Implementation of neural networks in PHP.
- 🎉 Intuitive and user-friendly interface.
- 🔬 Comprehensive unit tests to keep you on track!
- 🛠 Supports various optimizers including Adam, Adagrad, RMSprop, and basic Stochastic Gradient Descent (SGD)

## Technical Considerations 🤔

- PHP isn't Python, and that's okay! 🤷🏿‍♂️ Some things might behave a smidge differently.
- Decimal places got you down? Don't worry, our tests have got you covered with a tiny-tiny tolerance! 🎯
- NumpyLight was developed as an internal utility, mirroring the functionality of Python's Numpy.

## Acknowledgements 🙏🏿

- Gratitude to Harrison Kinsley for his pivotal work "Neural Networks from Scratch in Python"(https://nnfs.io/) ! 📚 You rock, man! 🤘🏿

## License 📝

MIT License (Feel free to fork, modify, and have fun! 🎉 Just give some credit where it's due, and you're golden! 🌟)
